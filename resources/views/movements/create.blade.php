<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            🔄 Nouveau Mouvement de Stock
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded-lg font-bold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('movements.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Produit à mouvementer</label>
                        <select name="product_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Actuel: {{ $product->quantity }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Type d'opération</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="Entrée">📈 Entrée (Achat/Don)</option>
                                <option value="Sortie">📉 Sortie (Vente/Perte)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Quantité</label>
                            <input type="number" name="quantity" min="1" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Motif du mouvement</label>
                        <input type="text" name="reason" placeholder="Ex: Vente client, Arrivage fournisseur..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 shadow-md">
                            Confirmer et mettre à jour le stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>