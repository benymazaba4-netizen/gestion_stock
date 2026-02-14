<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800">
            Modifier le produit : <span class="text-indigo-600">{{ $product->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
                
                <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nom du produit</label>
                        <input type="text" name="name" value="{{ $product->name }}" class="input-premium">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Quantité</label>
                            <input type="number" name="quantity" value="{{ $product->quantity }}" class="input-premium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Prix (FCFA)</label>
                            <input type="number" name="price" value="{{ $product->price }}" class="input-premium">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <a href="{{ route('products.index') }}" class="px-6 py-3 text-slate-500 font-bold hover:text-slate-800 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="btn-premium">
                            Mettre à jour
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>