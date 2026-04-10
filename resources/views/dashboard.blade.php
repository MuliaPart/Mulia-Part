<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <style>
        #formHarian, #formBulanan {
            transition: all 0.3s ease;
        }
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
        .card-line::after {
            background: linear-gradient(to right, #f0e65b, #070707);
        }
        .card-line::after {
            background: linear-gradient(to right, #facc15, #f59e0b); /* kuning bagus */
        }
        </style>
    <div class="py-6 px-6 h-700px">
        <form method="GET" class="flex items-center gap-3 mb-6 flex-wrap">

            <!-- MODE -->
            <select name="mode" id="modeSelect"
                class="border px-3 py-2 rounded w-40 text-center">
                <option value="harian" {{ $mode == 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="bulanan" {{ $mode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            </select>

            <!-- ================= HARIAN ================= -->
            <div id="formHarian" class="flex items-center gap-2">
                <input type="date"
                    name="tanggal"
                    value="{{ $tanggal }}"
                    class="border px-3 py-2 rounded">
            </div>

            <!-- ================= BULANAN ================= -->
            <div id="formBulanan" class="flex items-center gap-2">

                <select name="bulan" class="border px-3 py-2 rounded w-40">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <input type="number"
                    name="tahun"
                    value="{{ $tahun }}"
                    class="border px-3 py-2 rounded w-28">
            </div>

            <!-- BUTTON -->
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded">
                Filter
            </button>

        </form>
        <div class="bg-white p-3 leading-tight shadow-lg " style="height: 750px">
            <!-- KARTU RINGKASAN -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 max-w-8xl mx-auto mb-5 p-4" style="height: 150px">
                <div class="line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Total Omset</p>
                    <h2 class="text-2xl ml-8 mt-5">
                        Rp {{ number_format($totalOmset ?? 0,0,',','.') }}
                    </h2>
                </div>   
                <div class="line-card primary-line bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Penjualan Sparepart</p>
                        <h2 class="text-2xl ml-8 mt-5">
                            Rp {{ number_format($totalSparepart ?? 0,0,',','.') }}
                        </h2>
                </div>

                <div class="line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Penjualan Jasa</p>
                    <h2 class="text-2xl ml-8 mt-5">
                        Rp {{ number_format($totalJasa ?? 0,0,',','.') }}
                    </h2>
                </div>
                <div class=" line-card bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 shadow-lg">
                    <p class="text-1xl ml-5 mt-4">Total Pembayaran Transfer</p>
                    <h2 class="text-2xl ml-8 mt-5">
                        Rp {{ number_format($totalTransfer ?? 0,0,',','.') }}
                    </h2>
                </div>
                <!-- KANAN (1 bagian) -->
                <div class="line-card danger-line bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-2 h-300px shadow-lg border-0 position-relative">
                    <p class="text-1xl ml-5 mt-4">Total Cash</p>
                    <h2 class="text-2xl ml-8 mt-5">
                        Rp {{ number_format($totalCash ?? 0,0,',','.') }}
                    </h2>
                </div>
            </div>
                            
            <div class="grid grid-cols-1 md:grid-cols-10">
                <!-- BAGIAN GRAFIK PENJAUALAN -->
                <div class="md:col-span-7 card p-10 mt-4" style="height:500px; width:1200px;">
                    <h5>
                        Grafik Penjualan Bulan
                            {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} 
                            {{ $tahun }}
                    </h5>
                    <canvas id="dashboardChart"></canvas>
                </div>
                <!-- BAGIAN TRANSAKSI TERAKHIR -->
                <div class="md:col-span-3 bg-white font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight p-6 shadow-lg" style="height:500px;">
                    <p class="text-1xl ml-5 mt-4">Transaksi Terakhir</p>
                        @forelse($lastTransactions as $trx)
                            
                            @php
                                $colorClass = match(strtolower($trx->payment_method)) {
                                    'cash' => 'success-line',
                                    'transfer', 'qris' => 'card-line',
                                    'hutang' => 'danger-line',
                                    default => 'primary-line'
                                };
                            @endphp

                            <div 
                                    class="line-card {{ $colorClass }} mb-2 cursor-pointer"
                                    onclick="showDetail({{ $trx->id }})"
                                >

                                <p class="flex text-base mb-1">
                                    {{ $trx->invoice }} :
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('d-m-Y H:i') }}
                                </p>

                                <p class="text-lg ml-3 font-semibold">
                                    Rp {{ number_format($trx->total_price,0,',','.') }}
                                    <span class="text-sm ml-2 text-gray-600">
                                        ({{ ucfirst($trx->payment_method) }})
                                    </span>
                                </p>

                            </div>

                        @empty

                            <div class="text-center text-gray-500 mt-5">
                                Belum ada transaksi hari ini
                            </div>

                        @endforelse
                </div>
            </div>
        </div>
    </div>
    <div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">

        <div class="bg-white w-2/3 max-h-[80vh] overflow-y-auto rounded-lg p-6">

            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-bold">Detail Transaksi</h2>
                <button onclick="closeModal()" class="text-red-500 text-xl">✕</button>
            </div>

            <div id="modalContent">
                <!-- isi dari JS -->
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
    document.addEventListener("DOMContentLoaded", function () {

        const modeSelect = document.getElementById("modeSelect");
        const formHarian = document.getElementById("formHarian");
        const formBulanan = document.getElementById("formBulanan");

        function toggleForm() {
            if (modeSelect.value === "harian") {
                formHarian.style.display = "flex";
                formBulanan.style.display = "none";
            } else {
                formHarian.style.display = "none";
                formBulanan.style.display = "flex";
            }
        }

        // jalan saat load
        toggleForm();

        // jalan saat ganti mode
        modeSelect.addEventListener("change", toggleForm);

    });
    function showDetail(id) {

        fetch(`/transaction/${id}`)
            .then(res => res.json())
            .then(data => {

                let html = `
                    <p><b>Invoice:</b> ${data.invoice}</p>
                    <p><b>Tanggal:</b> ${data.created_at}</p>
                    <p><b>Metode:</b> ${data.payment_method}</p>
                    <hr class="my-2">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left">Nama</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                html += `
                    <p><b>Keterangan:</b> ${data.note ?? '-'}</p>
                `;
                data.items.forEach(item => {

                    html += `
                        <tr class="border-b">
                            <td>${item.name}</td>
                            <td class="text-center">${item.qty}</td>
                            <td class="text-center">${formatRupiah(item.sell_price)}</td>
                            <td class="text-right">${formatRupiah(item.total_price)}</td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                    <p><b>Keterangan:</b> ${data.note ?? '-'}</p>
                    <hr class="my-2">
                    <h3 class="text-right font-bold">
                        Total: ${formatRupiah(data.total_price)}
                    </h3>
                `;

                document.getElementById('modalContent').innerHTML = html;

                document.getElementById('modalDetail').classList.remove('hidden');
                document.getElementById('modalDetail').classList.add('flex');
            });
    }

    function closeModal(){
        document.getElementById('modalDetail').classList.add('hidden');
    }
    function formatRupiah(angka){
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }
</script>
</x-app-layout>
