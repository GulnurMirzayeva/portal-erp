<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // İlkin təsnifat siyahısı
        $initialClassifications = [
            'Maaş',
            'Bonus',
            'Premiya',
            'Avans',
            'Vergi',
            'Arenda',
            'Aylıq xərc',
            'Тех',
            'Taksi',
            'PPX 1',
            'PPX 2',
            'Uçastkovı',
            'Reklam',
            'Franşiza',
            'Mal-material',
            'FHN',
            'Depozit 1 %',
            'Kredit',
            'Komissiya kartı',
            'Питание',
            'Yeni oyun',
            'Sığorta yığım',
            'Digər',
        ];

        $now = now();
        $records = [];
        $order = 1;
        foreach ($initialClassifications as $name) {
            $records[] = [
                'name' => $name,
                'sort_order' => $order++,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('expense_classifications')->insert($records);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_classifications');
    }
};
