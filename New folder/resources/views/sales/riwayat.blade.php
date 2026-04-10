<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Riwayat Produk Terjual
        </h2>
    </x-slot>
    <div class="flex justify-end mr-6 mt-3">
        <form action="{{ route('sales.riwayat.deleteAll') }}" 
            method="POST"
            onsubmit="return confirm('Yakin ingin menghapus SEMUA Riwayat Kas ?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
                Hapus Riwayat Kas
            </button>
        </form>
    </div>

    <div class="py-6 px-6">
        <div class="bg-white rounded-2xl shadow-md border p-3">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <colgroup>
                            <col style="width:3%">
                            <col style="width:7%">
                            <col style="width:10%">
                            <col style="width:auto%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:10%">
                    </colgroup>
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-center">No</th>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-left">Nama Produk</th>
                            <th class="p-3 text-center">Terjual</th>
                            <th class="p-3 text-center">Harga Pokok</th>
                            <th class="p-3 text-center">Total Harga Jual</th>
                            <th class="p-3 text-center">Laba</th>
                            <th class="p-3 text-center">Tanggal</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="overflow-y-auto" style="height: 650px;">
                <table>
                    <colgroup>
                            <col style="width:3%">
                            <col style="width:7%">
                            <col style="width:10%">
                            <col style="width:auto%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:10%">
                    </colgroup>
                    <tbody>
                        @forelse($sales as $index => $sale)
                            <tr>
                                <td class="p-3 text-center">
                                    {{ $sales->firstItem() + $index }}
                                </td>

                                <td class="p-3">{{ $sale->category }}</td>
                                <td class="p-3">{{ $sale->code }}</td>
                                <td class="p-3">{{ $sale->name }}</td>
                                <td class="p-3 text-center">{{ $sale->terjual }}</td>
                                <td class="p-3">
                                    Rp {{ number_format($sale->total_harga_pokok) }}
                                </td>
                                <td class="p-3 text-center">
                                    Rp {{ number_format($sale->total_harga_jual) }}
                                </td>
                                <td class="p-3 text-center">
                                    Rp {{ number_format($sale->total_laba) }}
                                </td>
                                <td class="p-3 text-center">
                                    {{ $sale->created_at->format('d-m-Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-4 text-center text-gray-500">
                                    Belum ada data riwayat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
