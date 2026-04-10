<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Riwayat Produk Terjual Kasir
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="flex justify-end mb-3">

            <form
                action="{{ route('sales.riwayat.deleteAll') }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus SEMUA riwayat kasir ?')"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow"
                >
                    Hapus Semua Riwayat
                </button>

            </form>

        </div>

        <div class="bg-white rounded-2xl shadow-md border p-3">

            {{-- HEADER TABEL --}}
            <div class="overflow-x-auto">

                <table class="min-w-full text-sm border-collapse">

                    <colgroup>
                        <col style="width:3%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:10%">
                        <col style="width:auto%">
                        <col style="width:7%">
                        <col style="width:4%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:10%">
                    </colgroup>

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-center">No</th>
                            <th class="p-3 text-center">Invoice</th> 
                            <th class="p-3 text-center">Kategori</th>
                            <th class="p-3 text-center">Kode</th>
                            <th class="p-3 text-left">Nama Produk</th>
                            <th class="p-3 text-center">Harga Pokok</th>
                            <th class="p-3 text-center">Jumlah</th>
                            <th class="p-3 text-center">Harga Jual</th>
                            <th class="p-3 text-center">Diskon</th> 
                            <th class="p-3 text-center">Total Harga</th>
                            <th class="p-3 text-center">Tanggal</th>
                        </tr>
                    </thead>

                </table>

            </div>

            {{-- BODY TABEL --}}
            <div class="overflow-y-auto" style="height:650px;">

                <table class="min-w-full text-sm border-collapse">

                    <colgroup>
                        <col style="width:3%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:10%">
                        <col style="width:auto%">
                        <col style="width:7%">
                        <col style="width:4%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:10%">
                    </colgroup>

                    <tbody>

                        @forelse($sales as $index => $sale)

                        <tr class="border-b hover:bg-gray-50">

                            {{-- NO --}}
                            <td class="p-3 text-center">
                                {{ $sales->firstItem() + $index }}
                            </td>

                            {{-- INVOICE --}}
                            <td>
                                {{ $sale->transaction->invoice ?? '-' }}
                            </td>

                            {{-- KATEGORI --}}
                            <td class="p-3">
                                {{ $sale->category }}
                            </td>

                            {{-- KODE --}}
                            <td class="p-3 text-center">
                                {{ $sale->code }}
                            </td>

                            {{-- NAMA --}}
                            <td class="p-3">
                                {{ $sale->name }}
                            </td>

                            {{-- HARGA POKOK --}}
                            <td class="p-3 text-center">
                                Rp {{ number_format($sale->cost_price) }}
                            </td>

                            {{-- QTY --}}
                            <td class="p-3 text-center">
                                {{ $sale->qty }}
                            </td>

                            {{-- HARGA JUAL --}}
                            <td class="p-3 text-center">
                                Rp {{ number_format($sale->sell_price) }}
                            </td>

                            {{-- DISKON --}}
                            @php
                            $diskon = ($sale->sell_price * $sale->qty) - $sale->total_price;
                            @endphp

                            <td class="p-3 text-center {{ $diskon > 0 ? 'text-red-600 font-bold' : '' }}">
                                Rp {{ number_format($diskon) }}
                            </td>

                            {{-- TOTAL --}}
                            <td class="p-3 text-center">
                                Rp {{ number_format($sale->total_price) }}
                            </td>

                            {{-- TANGGAL --}}
                            <td class="p-3 text-center">
                                {{ $sale->created_at->format('d-m-Y H:i') }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="11" class="p-4 text-center text-gray-500">
                                Belum ada data riwayat
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $sales->links() }}
            </div>

        </div>

    </div>

</x-app-layout>