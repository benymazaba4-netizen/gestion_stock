<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase italic">✏️ Modifier : {{ $product->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf @method('PATCH')

                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Désignation</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-xl border-gray-200" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Catégorie</label>
                        <select name="category_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Prix (FCFA)</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full rounded-xl border-gray-200" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Stock Actuel</label>
                            <input type="number" name="quantity" value="{{ $product->quantity }}" class="w-full rounded-xl border-gray-100 bg-gray-50 text-gray-400" readonly>
                            <p class="text-[9px] text-orange-500 mt-1 italic">Utilisez le menu "Mouvements" pour ajuster le stock.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('products.index') }}" class="text-[10px] font-black uppercase text-gray-400 py-3">Annuler</a>
                        <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase shadow-lg">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>