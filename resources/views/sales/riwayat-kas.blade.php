<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Riwayat Kas
            </h2>
    </x-slot>
    <div class="flex justify-end mr-6 mt-3">
        <form action="{{ route('sales.riwayatKas.deleteAll') }}" 
            method="POST"
            onsubmit="return confirm('Yakin ingin menghapus SEMUA riwayat produk?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
                Hapus Semua Riwayat
            </button>
        </form>
    </div>
    <div name="body"class="bg-white rounded-2xl shadow-md border p-6 ml-20 mr-20 mt-3">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed border-collapse">

                <colgroup>
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
                    <tr>
                        <th>No</th>
                        <th>Omset</th>
                        <th>Jasa</th>
                        <th>Sparepart</th>
                        <th>Diskon</th>
                        <th>Hutang</th>
                        <th>Transfer</th>
                        <th>Total Cash</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="overflow-y-auto mt-4" style="height: 650px;">
            <table class="min-w-full text-gray border-collapse">
                <colgroup>
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
                <tbody>
                @foreach($histories as $index => $row)
                <tr class="text-sm border-b">

                    <td class="p-3 text-center">{{ $index + 1 }}</td>

                    <td class="p-3 text-center">Rp. {{ number_format($row->total_omset,0,',','.') }}</td>

                    <td class="p-3 text-center">Rp. {{ number_format($row->total_jasa,0,',','.') }}</td>

                    <td class="p-3 text-center">Rp. {{ number_format($row->total_sparepart,0,',','.') }}</td>

                    <td class="p-3 text-center text-red-600">
                        Rp. {{ number_format($row->total_diskon,0,',','.') }}
                    </td>

                    <td class="p-3 text-center">
                        Rp. {{ number_format($row->total_hutang,0,',','.') }}
                    </td>

                    <td class="p-3 text-center">
                        Rp. {{ number_format($row->total_transfer,0,',','.') }}
                    </td>

                    <td class="p-3 text-center font-bold text-green-600">
                        Rp. {{ number_format($row->total_cash,0,',','.') }}
                    </td>

                    <td class="p-3 text-center">
                        {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>                
        </div>

    </div>
</x-app-layout>