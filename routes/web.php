<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Category;
use App\Models\Movement;
use App\Models\User;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| 0. ROUTE DE SECOURS (Création des 3 profils de test)
|--------------------------------------------------------------------------
| Accède à : https://gestion-stock-49c5.onrender.com/force-register
*/
Route::get('/force-register', function () {
    try {
        // 1. L'ADMINISTRATEUR
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. L'OBSERVATEUR
        $observateur = User::updateOrCreate(
            ['email' => 'obs@gmail.com'],
            [
                'name' => 'Jean Observateur',
                'password' => Hash::make('password123'),
                'role' => 'observateur',
            ]
        );

        // 3. LE GESTIONNAIRE
        $gestionnaire = User::updateOrCreate(
            ['email' => 'stock@gmail.com'],
            [
                'name' => 'Marie Gestionnaire',
                'password' => Hash::make('password123'),
                'role' => 'gestionnaire',
            ]
        );

        return "<h3>Succès ! Les comptes sont créés :</h3>" . 
               "<ul>" .
               "<li><b>Admin :</b> admin@gmail.com</li>" .
               "<li><b>Observateur :</b> obs@gmail.com</li>" .
               "<li><b>Gestionnaire :</b> stock@gmail.com</li>" .
               "</ul>" .
               "<p>Mot de passe commun : <b>password123</b></p>" .
               "<a href='/login'>Aller à la page de connexion</a>";

    } catch (\Exception $e) {
        return "Erreur lors de la création : " . $e->getMessage();
    }
});

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

        Route::get('/create', function () {
            return view('products.create', ['categories' => Category::all()]);
        })->name('create');

        Route::post('/', function (Request $request) {
            Product::create($request->all());
            return redirect()->route('products.index')->with('success', 'Produit ajouté.');
        })->name('store');

        Route::get('/{product}/edit', function (Product $product) {
            return view('products.edit', ['product' => $product, 'categories' => Category::all()]);
        })->name('edit');

        Route::put('/{product}', function (Request $request, Product $product) {
            $product->update($request->all());
            return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
        })->name('update');

        Route::delete('/{product}', function (Product $product) {
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
            return view('categories.create');
        })->name('create');

        Route::post('/', function (Request $request) {
            Category::create($request->all());
            return redirect()->route('categories.index')->with('success', 'Catégorie créée.');
        })->name('store');

        Route::get('/{category}/edit', function (Category $category) {
            return view('categories.edit', compact('category'));
        })->name('edit');

        Route::put('/{category}', function (Request $request, Category $category) {
            $category->update($request->all());
            return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour.');
        })->name('update');

        Route::delete('/{category}', function (Category $category) {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Catégorie supprimée.');
        })->name('destroy');
    });

    // --- UTILISATEURS (Gestion des rôles) ---
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', function () {
            return view('users.index', ['users' => User::all()]);
        })->name('index');

        Route::patch('/{user}/role', function (Request $request, User $user) {
            $user->update(['role' => $request->role]);
            return redirect()->route('users.index')->with('success', 'Rôle mis à jour.');
        })->name('updateRole');

        Route::delete('/{user}', function (User $user) {
            if ($user->id !== auth()->id()) {
                $user->delete();
            }
            return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
        })->name('destroy');
    });

    // --- MOUVEMENTS DE STOCK ---
    Route::prefix('movements')->name('movements.')->group(function () {
        Route::get('/', function () {
            return view('movements.index', ['movements' => Movement::with(['product', 'user'])->latest()->get()]);
        })->name('index');

        Route::get('/create', function () {
            return view('movements.create', ['products' => Product::all()]);
        })->name('create');

        Route::post('/', function (Request $request) {
            Movement::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
            ]);

            $product = Product::find($request->product_id);
            if($request->type == 'in') { $product->increment('quantity', $request->quantity); }
            else { $product->decrement('quantity', $request->quantity); }

            return redirect()->route('movements.index')->with('success', 'Stock mis à jour.');
        })->name('store');
    });

    // --- EXPORTATION ---
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