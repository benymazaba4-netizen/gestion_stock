<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 uppercase italic">📦 Inventaire Global</h2>
            @if(Auth::user()->role !== 'observer')
                <a href="{{ route('products.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-black transition shadow-md">
                    + Nouveau Produit
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Désignation</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Catégorie</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-center">Stock</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Prix Unitaire</th>
                            @if(Auth::user()->role !== 'observer')
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-gray-800">{{ $product->name }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold uppercase">
                                        {{ $product->category->name ?? 'Non classé' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="font-mono font-bold {{ $product->quantity < 5 ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm font-mono text-gray-500">{{ number_format($product->price, 0) }} FCFA</td>
                                @if(Auth::user()->role !== 'observer')
                                    <td class="p-4 text-right flex justify-end gap-4">
                                        <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-black text-[10px] font-black uppercase">Modifier</a>
                                        @if(Auth::user()->role === 'admin')
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-500 hover:text-red-800 text-[10px] font-black uppercase">Supprimer</button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>