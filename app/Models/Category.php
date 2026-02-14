<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * RELATION INVERSE : Une catégorie possède plusieurs produits.
     * C'est ce qui permet d'utiliser $category->products_count dans la vue.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}