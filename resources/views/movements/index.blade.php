<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 uppercase">🕒 Journal des Flux</h2>
            @if(Auth::user()->role !== 'observer')
                <a href="{{ route('movements.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-700 transition">
                    + Nouveau Mouvement
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Date</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Produit</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-center">Type</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-center">Quantité</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-right">Auteur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($movements as $m)
                            <tr class="text-sm">
                                <td class="p-4 text-gray-400 italic">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4 font-bold">{{ $m->product->name }}</td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase {{ $m->type == 'Entrée' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $m->type }}
                                    </span>
                                </td>
                                <td class="p-4 text-center font-bold">{{ $m->quantity }}</td>
                                <td class="p-4 text-right text-xs text-gray-500 italic">{{ $m->user->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>