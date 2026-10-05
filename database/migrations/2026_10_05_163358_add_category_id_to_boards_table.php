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
        Schema::table('boards', function (Blueprint $table) {
            // On ajoute la clé étrangère (nullable si un board n'a pas de catégorie / Uncategorized)
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            
            // Optionnel : tu peux supprimer ton ancienne colonne 'tag' si tu ne t'en sers plus
            $table->dropColumn('tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            //
        });
    }
};
