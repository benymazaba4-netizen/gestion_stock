<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de Bord - Gestion de Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-blue-500">
                    <div class="text-sm text-gray-500 uppercase font-bold">Total Produits</div>
                    <div class="text-3xl font-bold">{{ $totalProduits }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-green-500">
                    <div class="text-sm text-gray-500 uppercase font-bold">Valeur Totale</div>
                    <div class="text-3xl font-bold">{{ number_format($valeurStock, 2) }} FCFA</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-red-500">
                    <div class="text-sm text-gray-500 uppercase font-bold">Alertes (Stock < 5)</div>
                    <div class="text-3xl font-bold text-red-600">{{ $alertesStock }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 italic">Répartition des quantités par produit</h3>
                <canvas id="stockChart" style="max-height: 400px;"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stockChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Quantité disponible',
                        data: {!! json_encode($data) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</x-app-layout>