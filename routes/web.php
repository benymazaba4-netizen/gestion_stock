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

// ==========================================================
// 0. ROUTE DE SECOURS (À SUPPRIMER APRÈS CONNEXION)
// Accède à : https://gestion-stock-49c5.onrender.com/force-register
// ==========================================================
Route::get('/force-register', function () {
    try {
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Évite les doublons si tu rafraîchis la page
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
        return "Succès ! L'utilisateur " . $user->email . " est prêt. <a href='/login'>Se connecter ici</a>";
    } catch (\Exception $e) {
        return "Erreur lors de la création : " . $e->getMessage();
    }
});

// 1. Accueil : Redirection vers le Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. Routes protégées (Authentification requise)
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD (Statistiques) ---
    Route::get('/dashboard', function () {
        $totalProduits = Product::count();
        $valeurStock = Product::sum(DB::raw('price * quantity'));
        $alertesStock = Product::where('quantity', '<', 5)->count();
        $labels = Product::pluck('name');
        $data = Product::pluck('quantity');
        return view('dashboard', compact('totalProduits', 'valeurStock', 'alertesStock', 'labels', 'data'));
    })->name('dashboard');

    // --- PRODUITS ---
    Route::get('/products', function () {
        return view('products.index', ['products' => Product::with('category')->get()]);
    })->name('products.index');

    Route::get('/products/create', function () {
        return view('products.create', ['categories' => Category::all()]);
    })->name('products.create');

    Route::post('/products', function (Request $request) {
        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Produit ajouté.');
    })->name('products.store');

    Route::get('/products/{product}/edit', function (Product $product) {
        return view('products.edit', ['product' => $product, 'categories' => Category::all()]);
    })->name('products.edit');

    Route::put('/products/{product}', function (Request $request, Product $product) {
        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
    })->name('products.update');

    Route::delete('/products/{product}', function (Product $product) {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produit supprimé.');
    })->name('products.destroy');

    // --- CATÉGORIES ---
    Route::get('/categories', function () {
        return view('categories.index', ['categories' => Category::all()]);
    })->name('categories.index');

    Route::get('/categories/create', function () {
        return view('categories.create');
    })->name('categories.create');

    Route::post('/categories', function (Request $request) {
        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Catégorie créée.');
    })->name('categories.store');

    Route::get('/categories/{category}/edit', function (Category $category) {
        return view('categories.edit', compact('category'));
    })->name('categories.edit');

    Route::put('/categories/{category}', function (Request $request, Category $category) {
        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour.');
    })->name('categories.update');

    Route::delete('/categories/{category}', function (Category $category) {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée.');
    })->name('categories.destroy');

    // --- UTILISATEURS ---
    Route::get('/users', function () {
        return view('users.index', ['users' => User::all()]);
    })->name('users.index');

    Route::patch('/users/{user}/role', function (Request $request, User $user) {
        $user->update(['role' => $request->role]);
        return redirect()->route('users.index')->with('success', 'Rôle mis à jour.');
    })->name('users.updateRole');

    Route::delete('/users/{user}', function (User $user) {
        if ($user->id !== auth()->id()) {
            $user->delete();
        }
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    })->name('users.destroy');

    // --- MOUVEMENTS ---
    Route::get('/movements', function () {
        return view('movements.index', ['movements' => Movement::with(['product', 'user'])->latest()->get()]);
    })->name('movements.index');

    Route::get('/movements/create', function () {
        return view('movements.create', ['products' => Product::all()]);
    })->name('movements.create');

    Route::post('/movements', function (Request $request) {
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
    })->name('movements.store');

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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';