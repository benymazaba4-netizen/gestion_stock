<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase italic">
            📦 Ajouter un nouveau produit
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
                
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-xs font-black uppercase text-gray-400 mb-2">Désignation</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm" placeholder="Ex: Ordinateur Dell Latitude" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-black uppercase text-gray-400 mb-2">Catégorie du stock</label>
                        <select name="category_id" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm text-sm" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-black uppercase text-gray-400 mb-2">Prix Unitaire (FCFA)</label>
                            <input type="number" name="price" value="{{ old('price') }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm" required>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-gray-400 mb-2">Quantité en stock</label>
                            <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm" required>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-black uppercase text-gray-400 mb-2">Description / Notes</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-gray-500 uppercase hover:text-black transition">
                            Annuler
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-black text-white font-black py-3 px-8 rounded-xl text-xs uppercase transition shadow-lg tracking-widest">
                            Enregistrer le produit
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>