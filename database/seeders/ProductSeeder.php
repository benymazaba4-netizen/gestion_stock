<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Movement;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catInfo = Category::where('name', 'Informatique')->first();
        $catReseau = Category::where('name', 'Réseau')->first();

        $p1 = Product::create([
            'name' => 'Ordinateur Dell Latitude',
            'description' => 'PC Portable pour développeur Django',
            'price' => 450000,
            'quantity' => 12,
            'category_id' => $catInfo->id,
        ]);

        $p2 = Product::create([
            'name' => 'Routeur Cisco ISR',
            'description' => 'Matériel pour TP Packet Tracer',
            'price' => 150000,
            'quantity' => 3, // En alerte ( < 5 )
            'category_id' => $catReseau->id,
        ]);

        $p3 = Product::create([
            'name' => 'Switch Aruba 24p',
            'description' => 'Switch manageable couche 3',
            'price' => 85000,
            'quantity' => 20,
            'category_id' => $catReseau->id,
        ]);

        // Création de quelques mouvements pour le graphique "Doughnut"
        Movement::create([
            'product_id' => $p1->id,
            'type' => 'entree',
            'quantity' => 10,
            'user_id' => 1,
            'comment' => 'Stock initial'
        ]);

        Movement::create([
            'product_id' => $p2->id,
            'type' => 'sortie',
            'quantity' => 2,
            'user_id' => 2,
            'comment' => 'Sortie pour labo réseau'
        ]);
    }
}