<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <style>
        .line-card {
            position: relative;
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.3s ease;
        }

        .line-card:hover {
            transform: translateY(-4px);
        }

        .line-card::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 5px;
            width: 100%;
            border-radius: 0 0 12px 12px;
            background: linear-gradient(to right, #4e73df, #1cc88a);
        }
        .primary-line::after {
            background: linear-gradient(to right, #4e73df, #224abe);
        }

        .success-line::after {
            background: linear-gradient(to right, #1cc88a, #17a673);
        }

        .danger-line::after {
            background: linear-gradient(to right, #e74a3b, #be2617);
        }
        </style>
    <div class="py-6 px-6 h-700px">
        <div class="bg-white p-3 leading-tight shadow-lg " style="height: 750px">
            <!-- KARTU RINGKASAN -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 max-w-8xl mx-auto mb-5 p-4" style="height: 150px">
                <div class="line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Total Omset</p>
                    <h2 id="totalCash" class="text-2xl ml-8 mt-5">
                        Rp 
                    </h2>
                </div>   
                <div class="line-card primary-line bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Penjualan Sparepart</p>
                    <h2 id="totalCash" class="text-2xl ml-8 mt-5">
                        Rp 
                    </h2>
                </div>

                <div class="line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Penjualan Jasa</p>
                    <h2 id="totalCash" class="text-2xl ml-8 mt-5">
                        Rp 
                    </h2>
                </div>
                <div class=" line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Total Pembayaran Transfer</p>
                    <h2 id="totalCash" class="text-2xl ml-8 mt-5">
                        Rp 
                    </h2>
                </div>
                <!-- KANAN (1 bagian) -->
                <div class="line-card danger-line bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 h-300px shadow-lg border-0 position-relative">
                    <p class="text-1xl ml-5 mt-4">Total Cash</p>
                    <h2 id="totalCash" class="text-2xl ml-8 mt-5">
                        Rp 
                    </h2>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-10">
                <div class="md:col-span-7 card p-10 mt-4" style="height:500px; width:1200px;">
                    <h5>Grafik Penjualan</h5>
                    <canvas id="dashboardChart"></canvas>
                </div>
                <div class="md:col-span-3 bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-6 shadow-lg" style="height:500px;">
                    <p class="text-1xl ml-5 mt-4">Transaksi Terakhir</p>
                    <div class="bg-blue-500 text-white p-2 mb-2 mt-2 ml-2 mr-2 rounded-2xl shadow-lg">
                        <p class="text-sm mb-2 ml-6">Penjualan Jasa</p>
                        <h2 id="totalJasa" class="font-bold ml-10">
                            Rp. 100.000
                        </h2>
                        <h3 class="ml-10">
                            20-02-2026 20:55:23
                        </h3>
                    </div>
                    <div class="bg-blue-500 text-white p-2 mb-2 ml-2 mr-2 rounded-2xl shadow-lg">
                        <p class="text-sm mb-2 ml-6">Penjualan Jasa</p>
                        <h2 id="totalJasa" class="font-bold ml-10">
                            Rp. 100.000
                        </h2>
                        <h3 class="ml-10">
                            20-02-2026 20:55:23
                        </h3>
                    </div>
                    <div class="bg-blue-500 text-white p-2 mb-2 ml-2 mr-2 rounded-2xl shadow-lg">
                        <p class="text-sm mb-2 ml-6">Penjualan Jasa</p>
                        <h2 id="totalJasa" class="font-bold ml-10">
                            Rp. 100.000
                        </h2>
                        <h3 class="ml-10">
                            20-02-2026 20:55:23
                        </h3>
                    </div>
                    <div class="bg-blue-500 text-white p-2 mb-2 ml-2 mr-2 rounded-2xl shadow-lg">
                        <p class="text-sm mb-2 ml-6">Penjualan Jasa</p>
                        <h2 id="totalJasa" class="font-bold ml-10">
                            Rp. 100.000
                        </h2>
                        <h3 class="ml-10">
                            20-02-2026 20:55:23
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {

                const ctx = document.getElementById('dashboardChart');

                if (!ctx) return;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($dates ?? []),
                        datasets: [{
                            label: 'Total Omset',
                            data: @json($omsets ?? []),
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

            });
        </script>
</x-app-layout>
