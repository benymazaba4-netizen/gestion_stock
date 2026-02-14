<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On crée la table categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // ❌ SI TU AS UN BLOC "Schema::table('products', ...)" ICI, SUPPRIME-LE !
        // C'est ce bloc qui cause l'erreur 1146 car la table products n'existe pas encore.
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};