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
| 2. ROUTES PROTÉGÉES (AUTHENTIFICATION REQUISE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        $totalProduits = Product::count();
        $valeurStock = Product::sum(DB::raw('price * quantity')) ?? 0;
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

        Route::delete('/{product}', function (Product $product) {
            if (auth()->user()->role !== 'admin') abort(403);
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produit supprimé.');
        })->name('destroy');
    });

    // --- CATÉGORIES ---
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', function () {
            return view('categories.index', ['categories' => Category::all()]);
        })->name('index');

        Route::get('/create', function () {
            if (auth()->user()->role === 'observateur') abort(403);
            return view('categories.create');
        })->name('create');

        Route::post('/', function (Request $request) {
            if (auth()->user()->role === 'observateur') abort(403);
            Category::create($request->all());
            return redirect()->route('categories.index')->with('success', 'Catégorie créée.');
        })->name('store');

        Route::get('/{category}/edit', function (Category $category) {
            if (auth()->user()->role === 'observateur') abort(403);
            return view('categories.edit', compact('category'));
        })->name('edit');

        Route::put('/{category}', function (Request $request, Category $category) {
            if (auth()->user()->role === 'observateur') abort(403);
            $category->update($request->all());
            return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour.');
        })->name('update');

        Route::delete('/{category}', function (Category $category) {
            if (auth()->user()->role !== 'admin') abort(403);
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Catégorie supprimée.');
        })->name('destroy');
    });

    // --- MOUVEMENTS DE STOCK ---
    Route::prefix('movements')->name('movements.')->group(function () {
        Route::get('/', function () {
            return view('movements.index', ['movements' => Movement::with(['product', 'user'])->latest()->get()]);
        })->name('index');

        Route::get('/create', function () {
            if (auth()->user()->role === 'observateur') abort(403);
            return view('movements.create', ['products' => Product::all()]);
        })->name('create');

        Route::post('/', function (Request $request) {
            if (auth()->user()->role === 'observateur') abort(403);
            
            Movement::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
            ]);

            $product = Product::find($request->product_id);
            if($request->type == 'in') { $product->increment('quantity', $request->quantity); }
            else { $product->decrement('quantity', $request->quantity); }

            return redirect()->route('movements.index')->with('success', 'Mouvement enregistré.');
        })->name('store');
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

        Route::delete('/{user}', function (User $user) {
            if (auth()->user()->role !== 'admin' || $user->id === auth()->id()) abort(403);
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
        })->name('destroy');
    });

    // --- EXPORTATION CSV ---
    Route::get('/export', function () {
        $products = Product::with('category')->get();
        $fileName = 'export_inventaire_' . date('d-m-Y') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $callback = function() use($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nom', 'Categorie', 'Prix', 'Quantite', 'Valeur Totale']);
            foreach ($products as $p) {
                fputcsv($file, [$p->id, $p->name, $p->category->name ?? 'N/A', $p->price, $p->quantity, $p->price * $p->quantity]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    })->name('export.index');

    // --- PROFIL ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';