<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tənzimləmələr cədvəli
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Filiallar və QR tənzimləmələri
        Schema::create('attendance_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_branch_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('qr_token', 64)->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. İşçilər, Filial təyinatı və PIN kodlar
        Schema::create('attendance_workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_worker_id')->nullable()->index();
            $table->unsignedBigInteger('portal_branch_id')->nullable()->index();
            $table->foreignId('attendance_branch_id')->nullable()->constrained('attendance_branches')->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->string('registered_device_uuid', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Giriş-Çıxış jurnalı və Şübhəli/Yanlış cəhdlər
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id')->nullable()->index();
            $table->string('worker_name')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->string('branch_name')->nullable();
            $table->date('date')->index();
            $table->string('type', 20)->default('check_in'); // check_in, check_out
            $table->timestamp('scanned_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('distance_meters', 8, 2)->nullable();
            $table->string('device_uuid', 100)->nullable()->index();
            $table->string('device_model', 150)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('success'); // success, rejected, suspicious
            $table->string('rejection_reason', 50)->nullable(); // out_of_range, device_already_used, wrong_pin, etc.
            $table->text('rejection_details')->nullable();
            $table->boolean('is_violation')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('attendance_workers');
        Schema::dropIfExists('attendance_branches');
        Schema::dropIfExists('attendance_settings');
    }
};
