<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vahid işçi kartotekası və əlaqəli cədvəlləri yaradır.
     */
    public function up(): void
    {
        // 1. Əsas İşçi Cədvəli
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_worker_id')->nullable()->index(); // Keçmiş sayt satışlarını bağlamaq üçün
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('full_name')->index();
            $table->string('tabel_display_name', 100)->nullable(); // Tabeldə qısa ad (məs: Əliyev, Səlimova)
            $table->string('phone', 30)->nullable()->index();
            $table->string('pin_code', 10)->default('1234');
            $table->enum('gender', ['male', 'female'])->default('male')->index();
            $table->boolean('is_intern')->default(false)->index();
            $table->enum('status', ['active', 'inactive', 'dismissed'])->default('active')->index();
            $table->date('hire_date')->nullable();
            $table->date('dismissal_date')->nullable();
            $table->boolean('requires_qr')->default(true); // Kurator/Ofis üçün QR-ı opsional etmək imkanı
            $table->string('registered_device_uuid', 150)->nullable()->index();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. İşçinin Vəzifələri (Çoxlu vəzifə imkanı: adm-res, sup-res, koor-akt)
        Schema::create('employee_worker_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('worker_type_id')->constrained('worker_types')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['employee_id', 'worker_type_id'], 'emp_wt_unique');
        });

        // 3. İşçinin Filialları (1 əsas filial + Kuratorlar üçün çoxlu idarə etdiyi filiallar)
        Schema::create('employee_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('attendance_branch_id')->constrained('attendance_branches')->cascadeOnDelete();
            $table->unsignedBigInteger('portal_branch_id')->nullable()->index();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_branch_id'], 'emp_branch_unique');
        });

        // 4. İşçinin Baxdığı Oyunlar (requires_game olan rollar üçün çoxlu oyun: Drakula, Kor...)
        Schema::create('employee_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedBigInteger('portal_game_id')->index();
            $table->string('game_name')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'portal_game_id'], 'emp_game_unique');
        });

        // 5. Tarixçə Jurnalı (Stajorluqdan daimiyə keçid və status dəyişiklikləri)
        Schema::create('employee_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('event_type', 50)->index(); // intern_promoted, status_changed, role_changed, branch_changed
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->date('effective_date')->index();
            $table->string('note')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Cədvəlləri silir.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_status_histories');
        Schema::dropIfExists('employee_games');
        Schema::dropIfExists('employee_branches');
        Schema::dropIfExists('employee_worker_types');
        Schema::dropIfExists('employees');
    }
};
