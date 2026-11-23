<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dinamik işçi tipləri və vizual bonus xal qaydaları cədvəllərini yaradır.
     */
    public function up(): void
    {
        Schema::create('worker_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_worker_type_id')->nullable()->index();
            $table->string('name');
            $table->string('female_name')->nullable();
            $table->string('slug')->unique();
            $table->string('short_code', 30);
            $table->string('female_short_code', 30)->nullable();
            $table->string('scope', 20)->default('branch')->index(); // branch, office, both
            $table->string('category', 30)->default('operational')->index(); // operational, management, support
            $table->boolean('is_bonus_eligible')->default(false)->index();
            $table->boolean('can_have_intern')->default(false)->index();
            $table->boolean('requires_game')->default(false);
            $table->boolean('show_in_tabel')->default(true);
            $table->boolean('can_substitute')->default(true);
            $table->string('sale_slot_key', 40)->nullable()->index(); // receptionists, actors, operators, hostesses
            $table->integer('tabel_sort_order')->default(50)->index();
            $table->string('default_pay_model', 30)->default('per_shift'); // fixed_monthly, per_shift, per_game, hybrid, none
            $table->string('badge_color', 20)->default('#2563eb');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('worker_type_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_type_id')->constrained('worker_types')->cascadeOnDelete();
            $table->unsignedBigInteger('portal_game_id')->nullable()->index(); // NULL => bütün oyunlar üçün default qayda
            $table->string('game_name')->nullable();
            $table->decimal('points', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('portal_bonus_rule_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['worker_type_id', 'portal_game_id'], 'wt_bonus_rules_type_game_unique');
        });
    }

    /**
     * Cədvəlləri silir.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_type_bonus_rules');
        Schema::dropIfExists('worker_types');
    }
};

