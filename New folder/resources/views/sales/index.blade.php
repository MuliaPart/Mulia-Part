<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Pembukuan Penjualan Harian
        </h2>
    </x-slot>

    <div class="py-6 px-6 min-h-screen">
        <!-- KARTU RINGKASAN -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 max-w-7xl mx-auto mb-5">

            <!-- KIRI (1 bagian) -->
            <div class="md:col-span-3 bg-green-500 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-3xl mt-8 ml-4 mb-2 font-bold">Total Omset</p>
                <h2 id="totalOmset" class="text-4xl ml-4 font-bold">
                    Rp {{ number_format($totalOmsetAfterDiskon, 0, ',', '.') }}
                </h2>
            </div>

            <!-- KANAN (2 bagian) -->
            <div class="md:col-span-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-500 text-white p-6 rounded-2xl shadow-lg">
                    <p class="text-sm mb-2">Penjualan Jasa</p>
                    <h2 id="totalJasa" class="text-2xl font-bold">
                        Rp {{ number_format($totalJasa) }}
                    </h2>
                </div>

                <div class="bg-indigo-500 text-white p-6 rounded-2xl shadow-lg">
                    <p class="text-sm mb-2">Penjualan Sparepart</p>
                    <h2 id="totalSparepart" class="text-2xl font-bold">
                        Rp {{ number_format($totalSparepartAfterDiskon, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="bg-red-500 text-white p-6 rounded-2xl shadow-lg">
                    <p class="text-sm mb-2">Total Hutang Toko Mulia</p>
                    <h2 id="totalHutang" class="text-2xl font-bold">
                        Rp {{ number_format($totalHutang) }}
                    </h2>
                </div>

                <div class="bg-purple-500 text-white p-6 rounded-2xl shadow-lg">
                    <p class="text-sm mb-2">Total Pembayaran Transfer</p>
                    <h2 id="totalTransfer" class="text-2xl font-bold">
                        Rp {{ number_format($totalTransfer) }}
                    </h2>
                </div>

            </div>
             <!-- KANAN (1 bagian) -->
            <div class="md:col-span-3 bg-yellow-500 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-3xl mt-8 ml-4 mb-2 font-bold">Total Cash</p>
                <h2 id="totalCash" class="text-4xl ml-4 font-bold">
                    Rp {{ number_format($totalCash, 0, ',', '.') }}
                </h2>
            </div>
        </div>


        <!-- FORM Import Data Penjualan -->
        <div class="bg-white rounded-2xl shadow-md border p-6 mb-6 flex justify-between items-center">
               <!-- KIRI: Import -->
            <div class="bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Import Data Penjualan (CSV)
                </h3>

                <form action="{{ route('sales.import') }}" 
                    method="POST" 
                    enctype="multipart/form-data">
                    @csrf

                    <div class="flex justify-between items-center">
                        <input type="file" name="file"required
                            class="border rounded px-4 py-2">

                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded shadow ml-1">
                            Import
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Masukan Total Hutang
                </h3>
                <input type="number" 
                    name="total_hutang"
                    value="{{$totalHutang}}"
                    id="inputHutang"
                    placeholder="Masukkan Total Hutang"
                    class="form-control border rounded number_format" required>
            </div>
            <div class="bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Masukan Total Transfer
                </h3>
                <input type="number"
                    name="total_transfer"
                    value="{{$totalTransfer}}"
                    id="inputTransfer"
                    placeholder="Masukkan Total Transfer"
                    class="form-control border rounded number_format" required>
            </div>
            <div class="bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Masukan Total Diskon
                </h3>
                <form action="{{ route('daily-summary.updateDiskon') }}" method="POST">
                    @csrf
                    <input type="number"
                        name="total_diskon" 
                        class="form-control border rounded number_format"
                        value="{{$totalDiskon}}"
                        placeholder="Input Total Diskon"
                        required>
                </form>
            </div>
            <div class="mb-1 ">
                <form action="{{ route('sales.simpan.riwayat') }}" 
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menyimpan data penjualan ke riwayat?')">
                    @csrf

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow mt-11">
                        Simpan Data Penjualan
                    </button>
                </form>
            </div>

            <!-- KANAN: Delete All -->
            <form action="{{ route('sales.deleteAll') }}" 
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus SEMUA data penjualan?')">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow mt-11">
                    Hapus Semua
                </button>
            </form>
        </div>
        <!-- TABEL DATA PENJUALAN -->
        <div class="bg-white rounded-2xl shadow-md border p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Riwayat Penjualan
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-collapse">

                        <colgroup>
                            <col style="width:5%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:auto%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                        </colgroup>

                        <thead class="bg-gray-100">
                            <tr class="text-gray-700 text-sm uppercase border-b">
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Kategori</th>
                                <th class="p-3 text-left">Kode Produk</th>
                                <th class="p-3 text-left">Nama Produk</th>
                                <th class="p-3 text-center">Stok Awal</th>
                                <th class="p-3 text-center">Terjual</th>
                                <th class="p-3 text-center">Stok Akhir</th>
                                <th class="p-3 text-center">Total Harga Pokok</th>
                                <th class="p-3 text-center">Total Harga Jual</th>
                                <th class="p-3 text-center">Total Laba</th>
                            </tr>
                        </thead>
                </table>
            </div>
            <div class="overflow-y-auto mt-4" style="height: 500px;">
                <table class="min-w-full text-gray border-collapse">
                        <colgroup>
                            <col style="width:5%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:auto%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                        </colgroup>
                     @forelse($sales as $sale)
                     <tr class="text-sm">
                            <td class="p-3 text-center">{{ $sale->no }}</td>
                            <td class="p-3 text-center">{{ $sale->category }}</td>
                            <td class="p-3 text-center">{{ $sale->code }}</td>
                            <td class="p-3">{{ $sale->name }}</td>
                            <td class="p-3 text-center">{{ $sale->stock_awal }}</td>
                            <td class="p-3 text-center">{{ $sale->terjual }}</td>
                            <td class="p-3 text-center">{{ $sale->stock_akhir }}</td>
                            <td class="p-3 text-center">{{ number_format($sale->total_harga_pokok) }}</td>
                            <td class="p-3 text-center">{{ number_format($sale->total_harga_jual) }}</td>
                            <td class="p-3 text-center">{{ number_format($sale->total_laba) }}</td>
                     </tr>
                        @empty
                    <tr>
                        <td colspan="10" class="p-4 text-center text-gray-500">
                            Belum ada data penjualan
                        </td>
                    </tr>
                        @endforelse
                </table>                
            </div>

        </div>

    </div>
<script>
document.getElementById('inputHutang').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        fetch('/update-hutang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                total_hutang: this.value
            })
        }).then(() => location.reload());
    }
});

document.getElementById('inputTransfer').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        fetch('/update-transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                total_transfer: this.value
            })
        }).then(() => location.reload());
    }
});
function animateValue(id, start, end, duration) {
    let range = end - start;
    let current = start;
    let increment = end > start ? 1 : -1;
    let stepTime = Math.abs(Math.floor(duration / range));
    
    if (range === 0) return;

    let timer = setInterval(function() {
        current += increment * Math.ceil(range / 50);
        if ((increment > 0 && current >= end) || 
            (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        document.getElementById(id).innerText = 
            new Intl.NumberFormat('id-ID').format(current);
    }, 20);
}

// Jalankan saat halaman load
window.onload = function() {
    animateValue("totalOmset", 0, {{ $totalOmsetAfterDiskon }}, 800);
    animateValue("totalSparepart", 0, {{ $totalSparepartAfterDiskon }}, 800);
    animateValue("totalCash", 0, {{ $totalCash }}, 800);
    animateValue("totalJasa", 0, {{ $totalJasa }}, 800);
    animateValue("totalHutang", 0, {{ $totalHutang }}, 800);
    animateValue("totalTransfer", 0, {{ $totalTransfer }}, 800);
};
</script>
</x-app-layout>
