<?php

namespace Tests\Feature;

use App\Models\AttendanceBranch;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\AttendanceWorker;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceNoAutoCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function createBranchAndWorker(): array
    {
        $branch = AttendanceBranch::create([
            'name' => 'Ofis',
            'slug' => 'ofis',
            'qr_token' => 'test_token_123456',
            'latitude' => 40.4093,
            'longitude' => 49.8671,
            'radius_meters' => 300,
            'is_active' => true,
        ]);

        $worker = AttendanceWorker::create([
            'attendance_branch_id' => $branch->id,
            'name' => 'Test İşçi',
            'phone' => '0501234567',
            'pin_code' => '1234',
            'is_active' => true,
        ]);

        return [$branch, $worker];
    }

    public function test_worker_is_never_auto_checked_out_even_if_setting_was_enabled_in_db(): void
    {
        [$branch, $worker] = $this->createBranchAndWorker();

        // Köhnə bazada auto_checkout_enabled='1' qalsa belə sistem avtomatik çıxış etməməlidir
        AttendanceSetting::set('auto_checkout_enabled', '1');
        AttendanceSetting::set('auto_checkout_time', '06:00');
        AttendanceSetting::set('require_pin', '0');

        $this->assertFalse(AttendanceSetting::isAutoCheckoutEnabled());

        Carbon::setTestNow(Carbon::create(2026, 11, 10, 5, 30, 0, 'Asia/Baku'));

        $service = app(AttendanceService::class);
        $checkIn = $service->processAttendance(
            branchToken: $branch->qr_token,
            workerId: $worker->id,
            pinCode: null,
            userLat: 40.4093,
            userLng: 49.8671,
            deviceUuid: 'dev_test_1',
            deviceModel: 'Apple iPhone',
            ipAddress: '127.0.0.1',
            userAgent: 'TestAgent'
        );

        $this->assertTrue($checkIn['success']);
        $this->assertSame('check_in', $checkIn['type']);
        $this->assertSame('check_out', $checkIn['next_action']);

        // Saat 06:00 keçdikdən və ertəsi gün olduqdan sonra belə avtomatik çıxış yazılmamalıdır
        Carbon::setTestNow(Carbon::create(2026, 11, 11, 15, 0, 0, 'Asia/Baku'));

        $this->assertSame(0, $service->performAutoCheckout());
        $this->artisan('attendance:auto-checkout')->assertSuccessful();

        $worker->refresh();
        $this->assertNotNull($worker->currentCheckInLog());
        $this->assertSame('check_out', $worker->next_action);
        $this->assertSame(0, AttendanceLog::where('worker_id', $worker->id)->where('type', 'check_out')->count());

        // Yalnız işçinin özü skan edib çıxış vurduqda çıxış qeydə alınmalıdır
        $checkOut = $service->processAttendance(
            branchToken: $branch->qr_token,
            workerId: $worker->id,
            pinCode: null,
            userLat: 40.4093,
            userLng: 49.8671,
            deviceUuid: 'dev_test_1',
            deviceModel: 'Apple iPhone',
            ipAddress: '127.0.0.1',
            userAgent: 'TestAgent'
        );

        $this->assertTrue($checkOut['success']);
        $this->assertSame('check_out', $checkOut['type']);
        $this->assertSame('check_in', $checkOut['next_action']);

        Carbon::setTestNow();
    }

    public function test_stale_force_type_cannot_duplicate_checkout_or_checkin(): void
    {
        [$branch, $worker] = $this->createBranchAndWorker();
        AttendanceSetting::set('require_pin', '0');

        // İşçi hələ giriş etməyib (çıxış vəziyyətindədir), köhnə tabdan check_out göndərilir
        $response = $this->postJson(route('attendance.scan.submit', ['token' => $branch->qr_token]), [
            'worker_id' => $worker->id,
            'latitude' => 40.4093,
            'longitude' => 49.8671,
            'device_uuid' => 'dev_test_2',
            'device_model' => 'Apple iPhone',
            'type' => 'check_out',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'reason' => 'already_checked_out',
                'next_action' => 'check_in',
            ]);

        $this->assertSame(0, AttendanceLog::where('worker_id', $worker->id)->count());

        // İşçi normal giriş edir
        $inResp = $this->postJson(route('attendance.scan.submit', ['token' => $branch->qr_token]), [
            'worker_id' => $worker->id,
            'latitude' => 40.4093,
            'longitude' => 49.8671,
            'device_uuid' => 'dev_test_2',
            'device_model' => 'Apple iPhone',
            'type' => 'check_in',
        ]);
        $inResp->assertOk()->assertJson(['success' => true, 'type' => 'check_in']);

        // Köhnə tabdan təkrar check_in göndərilir -> bloklanmalıdır
        $dupIn = $this->postJson(route('attendance.scan.submit', ['token' => $branch->qr_token]), [
            'worker_id' => $worker->id,
            'latitude' => 40.4093,
            'longitude' => 49.8671,
            'device_uuid' => 'dev_test_2',
            'device_model' => 'Apple iPhone',
            'type' => 'check_in',
        ]);

        $dupIn->assertStatus(422)
            ->assertJson([
                'success' => false,
                'reason' => 'already_checked_in',
                'next_action' => 'check_out',
            ]);

        $this->assertSame(1, AttendanceLog::where('worker_id', $worker->id)->count());
    }

    public function test_scan_page_returns_no_store_cache_headers_and_settings_update_keeps_auto_checkout_disabled(): void
    {
        [$branch] = $this->createBranchAndWorker();

        $scanResp = $this->get(route('attendance.scan', ['token' => $branch->qr_token]));
        $scanResp->assertOk();
        $this->assertStringContainsString('no-store', (string) $scanResp->headers->get('Cache-Control'));

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $updateResp = $this->actingAs($admin)->post(route('attendance.settings.update'), [
            'require_pin' => '0',
            'default_radius_meters' => '200',
            'allow_device_reuse' => '0',
            'track_late_arrivals' => '0',
            'work_start_time' => '09:00',
            'late_tolerance_minutes' => '15',
        ]);

        $updateResp->assertRedirect(route('attendance.settings'));
        $this->assertSame('0', AttendanceSetting::get('auto_checkout_enabled'));
    }
}

