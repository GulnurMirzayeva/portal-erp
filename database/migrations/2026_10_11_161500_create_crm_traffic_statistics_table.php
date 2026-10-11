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
        Schema::create('crm_traffic_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedTinyInteger('month')->index();
            $table->unsignedTinyInteger('day');

            // 1. İnstaqram Mesajları
            $table->integer('instagram_messages')->default(0);

            // 2. İnstaqram İzləyiciləri (Yeni, Çıxan, Xalis Net)
            $table->integer('instagram_followers_new')->default(0);
            $table->integer('instagram_followers_lost')->default(0);
            $table->integer('instagram_followers_net')->default(0);

            // 3. Zənglər
            $table->integer('calls')->default(0);

            // 4. TikTok İzləyici Artımı / Azalması (Net dəyişim)
            $table->integer('tiktok_followers_net')->default(0);

            // 5. Sayt İstifadəçiləri (Unikal istifadəçilər - API və ya manual)
            $table->integer('website_users')->default(0);

            // 6. Sayta Daxilolmalar / Ziyarətlər (Visits / Sessions - API və ya manual)
            $table->integer('website_visits')->default(0);

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_traffic_statistics');
    }
};
