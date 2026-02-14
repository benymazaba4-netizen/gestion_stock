<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable(); // Ajouté pour le seeder
            $table->integer('quantity')->default(0);
            
            // On garde tes colonnes de prix mais on ajoute 'price' pour la compatibilité
            $table->decimal('price', 10, 2); 
            $table->decimal('price_buy', 10, 2)->nullable(); 
            $table->decimal('price_sell', 10, 2)->nullable(); 
            
            $table->integer('stock_min')->default(5);
            
            // CRUCIAL : La clé étrangère pour les catégories
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};