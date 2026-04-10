    <x-app-layout>

        <x-slot name="header">
            <h2 class="text-2xl font-bold">
                Laporan Bulanan
            </h2>
        </x-slot>

        <div class="p-6">

            <!-- FILTER -->
            <h3 class="text-lg text-gray-600 mt-2">
                Periode: {{ $namaBulan[$bulan] ?? '' }}
            </h3>
            <form method="GET" class="mb-6 flex gap-2">
                <select name="bulan" class="border px-2 py-1">
                    @php
                        $namaBulan = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp

                    @foreach($namaBulan as $key => $nama)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                <input type="number" name="tahun" value="{{ $tahun }}" class="border px-2 py-1" placeholder="Tahun">

                <button class="bg-blue-500 text-white px-4 rounded">
                    Filter
                </button>
            </form>

            <!-- KARTU -->
        <div class=" w-full bg-white p-4 shadow rounded mb-3 text-center">
            <div class=" w-full bg-gray-100 mb-2"><p class="text-xl text-gray-800">Aktiva Lancar</p></div>
            <div class=" w-full bg-white p-4 shadow rounded mb-3 text-center">
                <p class="text-xl text-gray-600">Total Omset</p>
                <h2 class="text-xl font-bold text-yellow-500">
                    Rp {{ number_format($totalOmset,0,',','.') }}
                </h2>
            </div>
            <div class="flex justify-between items-center w-full bg-white p-4  rounded">
                <p class="text-gray-600">Penjualan Jasa</p>
                <h2 class="text-xl font-bold">
                    Rp {{ number_format($totalJasa,0,',','.') }}
                </h2>
            </div>

            <div class="flex justify-between items-center w-full bg-white p-4  border-b rounded mb-3">
                <p class="text-gray-600">Penjualan Sparepart</p>
                <h2 class="text-xl font-bold">
                    Rp {{ number_format($totalSparepart,0,',','.') }}
                </h2>
            </div>
            <div class="flex justify-between items-center w-full bg-white p-4  border-b  rounded mb-3">
                <p class="text-gray-600">Belanja Modal</p>
                <h2 class="text-xl font-bold text-red-500">
                    Rp {{ number_format($totalModal,0,',','.') }}
                </h2>
            </div>
            <div class="flex justify-between items-center w-full bg-white p-4  rounded">
                <p class="text-gray-600">Laba Jasa</p>
                <h2 class="text-xl font-bold text-blue-600">
                    Rp {{ number_format($labaJasa,0,',','.') }}
                </h2>
            </div>

            <div class="flex justify-between items-center w-full bg-white p-4 border-b rounded mb-3">
                <p class="text-gray-600">Laba Sparepart</p>
                <h2 class="text-xl font-bold text-blue-600">
                    Rp {{ number_format($labaSparepart,0,',','.') }}
                </h2>
            </div>

            <div class="bg-white p-4 rounded col-span-3 text-center">
                <p class="text-lg">Laba Total</p>
                <h2 class="text-2xl font-bold text-green-700">
                    Rp {{ number_format($labaTotal,0,',','.') }}
                </h2>
            </div>

        </div>

    </x-app-layout>