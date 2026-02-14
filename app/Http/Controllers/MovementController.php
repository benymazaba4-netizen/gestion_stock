<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementController extends Controller
{
    /**
     * Affiche l'historique de tous les mouvements de stock.
     */
    public function index()
    {
        // On récupère les mouvements avec les relations pour optimiser les requêtes (Eager Loading)
        $movements = Movement::with(['product', 'user'])->latest()->get();
        return view('movements.index', compact('movements'));
    }

    /**
     * Affiche le formulaire pour enregistrer un nouveau mouvement.
     */
    public function create()
    {
        $products = Product::all();
        return view('movements.create', compact('products'));
    }

    /**
     * Logique métier : Enregistrement et mise à jour dynamique du stock.
     */
    public function store(Request $request)
    {
        // 1. Validation rigoureuse des données entrantes
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:Entrée,Sortie',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // 2. Sécurité Réseau/Métier : Empêcher le stock négatif
        if ($request->type === 'Sortie' && $product->quantity < $request->quantity) {
            return back()->withErrors([
                'quantity' => "Action impossible : Vous tentez de sortir {$request->quantity} unités, mais il n'en reste que {$product->quantity} en stock."
            ])->withInput();
        }

        // 3. Création du mouvement dans la base de données
        Movement::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'quantity' => $request->quantity,
        ]);

        // 4. Mise à jour atomique de la table Products
        if ($request->type === 'Entrée') {
            $product->increment('quantity', $request->quantity);
        } else {
            $product->decrement('quantity', $request->quantity);
        }

        return redirect()->route('movements.index')
            ->with('success', "Le mouvement ({$request->type}) pour le produit '{$product->name}' a été enregistré.");
    }

    /**
     * Exportation des données au format CSV avec encodage Excel.
     */
    public function exportCsv()
    {
        $fileName = 'Rapport_Stocks_' . date('d-m-Y_H-i') . '.csv';
        $movements = Movement::with(['product', 'user'])->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($movements) {
            $file = fopen('php://output', 'w');
            
            // Ajout du BOM UTF-8 pour la compatibilité Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes
            fputcsv($file, ['ID', 'DATE', 'PRODUIT', 'TYPE', 'QUANTITE', 'OPERATEUR'], ';');

            foreach ($movements as $m) {
                fputcsv($file, [
                    $m->id,
                    $m->created_at->format('d/m/Y H:i'),
                    $m->product->name,
                    $m->type,
                    $m->quantity,
                    $m->user->name
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}