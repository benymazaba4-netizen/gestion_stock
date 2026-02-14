<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // Tout le monde voit la liste (Admin, Manager, Observer)
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    // Seuls Admin et Manager (géré par le middleware des routes)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:255'
        ]);

        Category::create(['name' => $request->name]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    // Suppression autorisée pour Admin ET Manager
    public function destroy(Category $category)
    {
        // Sécurité supplémentaire au cas où
        if (Auth::user()->role === 'observer') {
            return back()->with('error', 'Action interdite pour votre profil.');
        }

        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Cette catégorie contient des produits, elle ne peut pas être supprimée.']);
        }

        $category->delete();
        return back()->with('success', 'Catégorie supprimée avec succès.');
    }
}