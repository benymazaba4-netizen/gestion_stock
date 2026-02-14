<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * LISTE : Affiche tous les produits avec leur catégorie.
     */
    public function index()
    {
        // On utilise 'with' pour charger les catégories en une seule requête (performance)
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    /**
     * CRÉATION : Affiche le formulaire avec la liste des catégories.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * ENREGISTREMENT : Sauvegarde le nouveau produit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Le produit a été ajouté au catalogue.');
    }

    /**
     * ÉDITION : Charge le produit et les catégories pour modification.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * MISE À JOUR : Enregistre les modifications.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Informations du produit mises à jour.');
    }

    /**
     * SUPPRESSION : (Route protégée par middleware role:admin dans web.php)
     */
    public function destroy(Product $product)
    {
        // Optionnel : vérifier si le produit a des mouvements avant de supprimer
        if ($product->movements()->count() > 0) {
            return back()->withErrors(['error' => 'Impossible de supprimer un produit qui possède un historique de mouvements.']);
        }

        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produit définitivement retiré du système.');
    }
}