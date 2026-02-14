<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase italic">📂 Répertoire des Catégories</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="md:col-span-1">
                    @if(Auth::user()->role !== 'observer')
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-[10px] font-black text-indigo-600 uppercase mb-4 tracking-widest">Nouveau Groupe</h3>
                            <form action="{{ route('categories.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nom</label>
                                    <input type="text" name="name" class="w-full rounded-xl border-gray-200 text-sm" placeholder="Ex: Informatique" required>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-black text-white font-black py-3 rounded-xl text-[10px] uppercase transition shadow-lg">
                                    Ajouter
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-200 text-center">
                            <span class="text-2xl opacity-50">🔒</span>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mt-2 italic">Lecture seule uniquement</p>
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Désignation</th>
                                    <th class="p-4 text-[10px] font-black text-gray-400 uppercase text-center">Quantité d'articles</th>
                                    @if(Auth::user()->role !== 'observer')
                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($categories as $category)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4 font-bold text-gray-800">{{ $category->name }}</td>
                                        <td class="p-4 text-center">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold">
                                                {{ $category->products_count }}
                                            </span>
                                        </td>
                                        
                                        {{-- MANAGER ET ADMIN PEUVENT SUPPRIMER --}}
                                        @if(Auth::user()->role !== 'observer')
                                            <td class="p-4 text-right">
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-500 hover:text-red-700 font-black text-[10px] uppercase">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>