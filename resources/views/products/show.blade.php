<x-app-layout>

<div class="p-6">

    <!-- ================= INFO PRODUK ================= -->
    <div class="bg-white shadow rounded-lg p-6 mb-6 flex gap-6">

        <img src="{{ asset('storage/'.$product->image) }}" 
             class="w-48 h-48 object-contain border rounded">

        <div class="flex-1">

            <h2 class="text-2xl font-bold mb-2">
                {{ $product->name }}
            </h2>

            <p><b>Kode:</b> {{ $product->code }}</p>
            <p><b>Kategori:</b> {{ $product->category->name ?? '-' }}</p>

            <p><b>Harga Pokok:</b> 
                Rp {{ number_format($product->cost_price,0,',','.') }}
            </p>

            <p><b>Harga Jual:</b> 
                Rp {{ number_format($product->sell_price,0,',','.') }}
            </p>

            <p><b>Stok:</b> 
                {{ $product->stock }}
            </p>

        </div>

    </div>

    <!-- ================= RESTOCK ================= -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">

        <h3 class="font-bold mb-3">Riwayat Restok</h3>

        <div class="overflow-y-auto max-h-64 border">

            <table class="w-full text-sm">

                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="p-2">Tanggal</th>
                        <th class="p-2">Jumlah</th>
                        <th class="p-2">Harga Pokok</th>
                        <th class="p-2">Supplier</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($product->restocks as $restock)
                    <tr class="border-b">
                        <td class="p-2 text-center">
                            {{ $restock->created_at->translatedFormat('d F Y H:i') }}
                        </td>
                        <td class="p-2 text-center">
                            {{ $restock->qty }}
                        </td>
                        <td class="p-2 text-center">
                            Rp {{ number_format($restock->cost_price,0,',','.') }}
                        </td>
                        <td class="p-2 text-center">
                            {{ $restock->supplier }}
                        </td>
                        <td class="p-2 text-center">

                            <form method="POST" action="{{ route('restock.delete', $restock->id) }}"
                                  onsubmit="return confirm('Hapus data restok?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

    <!-- ================= TERJUAL ================= -->
    <div class="bg-white shadow rounded-lg p-4">

        <h3 class="font-bold mb-3">Riwayat Terjual</h3>

        <div class="overflow-y-auto max-h-64 border">

            <table class="w-full text-sm">

                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="p-2">Tanggal</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Harga Jual</th>
                        <th class="p-2">Customer</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($product->transactionItems as $item)

                    <tr class="border-b text-center">

                        <td class="p-2">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}
                        </td>

                        <td class="p-2 text-center">
                            {{ $item->qty }}
                        </td>

                        <td class="p-2 text-center">
                            Rp {{ number_format($item->sell_price - ($item->discount ?? 0),0,',','.') }}
                        </td>

                        <td class="p-2 text-center">
                            {{ $item->transaction->customer_name ?? '-' }}
                        </td>

                        <td class="p-2 text-center">

                            <form method="POST" action="{{ route('transaction.item.delete', $item->id) }}"
                                  onsubmit="return confirm('Hapus data penjualan ini?')">
                                @csrf
                                @method('DELETE')

                                <button class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-app-layout>