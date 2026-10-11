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
        Schema::create('crm_inquiry_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedTinyInteger('month')->index(); // 1 to 12
            $table->string('period_label', 50)->nullable(); // e.g. "Mart 2026"

            // Sütunlar (Şəkildəki kimi ardıcıllıqla):
            $table->unsignedInteger('cancellations')->default(0);    // Отмена
            $table->unsignedInteger('discounts')->default(0);        // Скидка
            $table->unsignedInteger('reservations')->default(0);     // Резервация
            $table->unsignedInteger('location')->default(0);         // Местоположение
            $table->unsignedInteger('games')->default(0);            // Игры
            $table->unsignedInteger('working_hours')->default(0);    // График работы
            $table->unsignedInteger('complaints')->default(0);       // Жалобы
            $table->unsignedInteger('other')->default(0);            // Другое
            $table->unsignedInteger('total')->default(0);            // Общее

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_inquiry_statistics');
    }
};
