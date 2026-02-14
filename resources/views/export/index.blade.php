<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            📂 {{ __('Centre d\'Exportation et Rapports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
                <div class="text-center mb-10">
                    <div class="inline-block p-4 bg-indigo-50 rounded-full mb-4">
                        <span class="text-4xl">📊</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Générer vos rapports</h3>
                    <p class="text-gray-500 mt-2">Consultez ou exportez les données de votre gestion de stock.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-6 bg-gray-50 rounded-2xl border border-gray-200 hover:border-indigo-300 transition group">
                        <div class="flex items-center">
                            <div class="p-3 bg-white rounded-lg shadow-sm mr-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                                <span class="font-bold">CSV</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Historique des Flux</h4>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Toutes les entrées et sorties</p>
                            </div>
                        </div>

                        {{-- SEULS ADMIN ET MANAGER PEUVENT TÉLÉCHARGER --}}
                        @if(Auth::user()->role !== 'observer')
                            <a href="{{ route('movements.export') }}" class="bg-indigo-600 hover:bg-black text-white px-6 py-2 rounded-lg font-bold text-xs uppercase shadow-lg transition">
                                Télécharger
                            </a>
                        @else
                            <span class="text-[10px] font-bold text-orange-500 uppercase italic bg-orange-50 px-3 py-2 rounded-lg border border-orange-100">
                                Consultation seule
                            </span>
                        @endif
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg flex items-start text-blue-700">
                        <span class="mr-3">ℹ️</span>
                        @if(Auth::user()->role !== 'observer')
                            <p class="text-xs italic">Les fichiers CSV peuvent être exploités sur Excel pour vos inventaires périodiques.</p>
                        @else
                            <p class="text-xs italic">Votre compte (Observateur) ne possède pas les privilèges nécessaires pour exporter les données brutes.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>