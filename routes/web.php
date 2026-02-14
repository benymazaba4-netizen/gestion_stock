<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Movement;
use App\Models\User;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| 1. ACCUEIL ET REDIRECTION
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| 2. ROUTES PROTÉGÉES (AUTH REQUIS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        $totalProduits = Product::count();
        $valeurStock = Product::sum(DB::raw('price * quantity'));
        $alertesStock = Product::where('quantity', '<', 5)->count();
        $labels = Product::pluck('name');
        $data = Product::pluck('quantity');
        return view('dashboard', compact('totalProduits', 'valeurStock', 'alertesStock', 'labels', 'data'));
    })->name('dashboard');

    // --- PRODUITS ---
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', function () {
            return view('products.index', ['products' => Product::with('category')->get()]);
        })->name('index');

        // Protection : Seuls Admin et Gestionnaire peuvent créer/modifier
        Route::middleware(['can:manage-stock'])->group(function () {
            Route::get('/create', function () {
                if (auth()->user()->role === 'observateur') abort(403);
                return view('products.create', ['categories' => Category::all()]);
            })->name('create');

            Route::post('/', function (Request $request) {
                if (auth()->user()->role === 'observateur') abort(403);
                Product::create($request->all());
                return redirect()->route('products.index')->with('success', 'Produit ajouté.');
            })->name('store');

            Route::get('/{product}/edit', function (Product $product) {
                if (auth()->user()->role === 'observateur') abort(403);
                return view('products.edit', ['product' => $product, 'categories' => Category::all()]);
            })->name('edit');

            Route::put('/{product}', function (Request $request, Product $product) {
                if (auth()->user()->role === 'observateur') abort(403);
                $product->update($request->all());
                return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
            })->name('update');
        });

        // Seul l'Admin peut supprimer
        Route::delete('/{product}', function (Product $product) {
            if (auth()->user()->role !== 'admin') abort(403, "Seul l'admin peut supprimer.");
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produit supprimé.');
        })->name('destroy');
    });

    // --- CATÉGORIES ---
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', function () {
            return view('categories.index', ['categories' => Category::all()]);
        })->name('index');

        Route::middleware(['can:manage-stock'])->group(function () {
            Route::get('/create', function () {
                if (auth()->user()->role === 'observateur') abort(403);
                return view('categories.create');
            })->name('create');

            Route::post('/', function (Request $request) {
                if (auth()->user()->role === 'observateur') abort(403);
                Category::create($request->all());
                return redirect()->route('categories.index')->with('success', 'Catégorie créée.');
            })->name('store');
        });
    });

    // --- UTILISATEURS (ADMIN UNIQUEMENT) ---
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', function () {
            if (auth()->user()->role !== 'admin') abort(403);
            return view('users.index', ['users' => User::all()]);
        })->name('index');

        Route::patch('/{user}/role', function (Request $request, User $user) {
            if (auth()->user()->role !== 'admin') abort(403);
            $user->update(['role' => $request->role]);
            return redirect()->route('users.index')->with('success', 'Rôle mis à jour.');
        })->name('updateRole');
    });

    // --- MOUVEMENTS ---
    Route::get('/movements', function () {
        return view('movements.index', ['movements' => Movement::with(['product', 'user'])->latest()->get()]);
    })->name('index');

    // --- PROFIL ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';