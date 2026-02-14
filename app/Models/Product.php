<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Colonnes autorisées pour l'insertion de données
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id', // Indispensable pour lier à une catégorie
    ];

    /**
     * Relation : Un produit appartient à une seule catégorie.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation : Un produit peut avoir plusieurs mouvements (entrées/sorties).
     */
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}