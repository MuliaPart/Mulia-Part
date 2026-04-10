<x-app-layout>

    <div class="p-6">

        <h2 class="text-xl font-bold mb-4">Riwayat Transaksi</h2>

        <div class="bg-white shadow rounded-lg p-4 overflow-x-auto">
            <form 
                method="POST" 
                action="{{ route('riwayat.transaksi.deleteByDate') }}"
                onsubmit="return confirm('Yakin hapus semua transaksi di tanggal ini?')"
                class="flex justify-end mb-2"
            >
                @csrf

                <input type="date" name="tanggal" class="mr-2" required>

                <button class="bg-red-500 text-white px-3 py-2 rounded">
                    Hapus per Tanggal
                </button>
            </form>

            <table class="w-full text-sm border">

                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2">No</th>
                        <th class="p-2">Invoice</th>
                        <th class="p-2">Waktu</th>
                        <th class="p-2">Modal</th>
                        <th class="p-2">Harga Jual</th>
                        <th class="p-2">Diskon</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Pembayaran</th>
                        <th class="p-2">Member</th>
                        <th class="p-2">Customer</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($transactions as $i => $trx)

                    @php
                        $totalModal = 0;
                        $totalJual  = 0;
                        $totalDiskon = 0;

                        foreach($trx->items as $item){

                            $modal = $item->cost_price * $item->qty;
                            $jual  = $item->sell_price * $item->qty;

                            $totalModal += $modal;
                            $totalJual  += $jual;
                            $totalDiskon = $totalJual-$trx->total_price;
                        }
                    @endphp

                    <tr class="border-b hover:bg-gray-50">

                        <td class="p-2">{{ $i+1 }}</td>

                        <td class="p-2 font-semibold">
                            <button 
                                onclick="showDetail({{ $trx->id }})"
                                class="text-blue-600 hover:underline">
                                {{ $trx->invoice }}
                            </button>
                        </td>

                        <td class="p-2">
                            {{ \Carbon\Carbon::parse($trx->created_at)->format('d-m-Y H:i') }}
                        </td>

                        <td class="p-2">
                            Rp {{ number_format($totalModal,0,',','.') }}
                        </td>

                        <td class="p-2">
                            Rp {{ number_format($totalJual,0,',','.') }}
                        </td>

                        <td class="p-2 text-red-600">
                            Rp {{ number_format($totalDiskon,0,',','.') }}
                        </td>

                        <td class="p-2 text-green-600 font-bold">
                            Rp {{ number_format($trx->total_price,0,',','.') }}
                        </td>

                        <td class="p-2">
                            {{ strtoupper($trx->payment_method ?? '-') }}
                        </td>

                        <td class="p-2">
                            {{ $trx->customer_type == 'member' ? 'Member' : 'Non Member' }}
                        </td>

                        <td class="p-2">
                            {{ $trx->customer_name ?? '-' }}
                        </td>

                        <td class="p-2 text-center">

                            <form 
                                method="POST" 
                                action="{{ route('riwayat.transaksi.delete', $trx->id) }}"
                                onclick="return confirm('⚠️ Yakin hapus transaksi ini?\nData tidak bisa dikembalikan!')"
                            >
                                @csrf
                                @method('DELETE')

                                <button 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
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
<!-- MODAL DETAIL -->
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
    <script>
    
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
                        <p><b>Keterangan:</b> <p>${data.note ?? '-'}</p></p>
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