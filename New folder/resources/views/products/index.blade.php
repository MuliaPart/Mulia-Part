<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray leading-tight">
            Daftar Produk
        </h2>
    </x-slot>

<div class="px-6">

    {{-- ================= HEADER AREA (DIAM) ================= --}}
    <div class="bg-gray-500 p-4 rounded-lg sticky leading-tight">

        {{-- IMPORT --}}
        <form action="{{ route('products.import') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="mb-4 text-white">
            @csrf
            <input type="file" name="file" required>
            <button type="submit" 
                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow">
                Import Excel
            </button>
        </form>
        {{-- BUTTON AREA --}}
        <div class="flex justify-between items-center mb-4">

            <a href="{{ route('products.create') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded shadow">
                + Tambah Produk
            </a>
         {{-- SEARCH --}}
             <form method="GET" action="{{ route('products.index') }}" class="mb-1 px-10 py-1">
                <div class="flex gap-3 px-10 py-1">
                    <input type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama / kode produk..."
                        class="px-10 py-2 w-64 text-black rounded">
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                        Search
                    </button>
                </div>
            </form>
            <form action="{{ route('products.deleteAll') }}" 
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus semua data produk?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                    Hapus Semua Produk
                </button>
            </form>
        </div>

    </div>


    {{-- ================= TABLE SCROLL AREA ================= --}}
    

        <table class="min-w-full table-fixed text-gray">
            <colgroup>
                <col style="width:1%">
                <col style="width:7%">
                <col style="width:8%">
                <col style="width:auto%">
                <col style="width:7%">
                <col style="width:8%">
                <col style="width:3%">
                <col style="width:5%">
            </colgroup>

            <thead class="border-b border-gray-600 sticky top-0 z-30">
                <tr class="border-b border-gray-700">
                    <th class="w-16">No</th>
                    <th class="w-32">Kategori</th>
                    <th class="p-2 ml-10 w-32">Kode</th>
                    <th class="width 200">Nama Produk</th>
                    <th class="w-32 text-center">Harga Pokok</th>
                    <th class="w-32 text-center">Harga Jual</th>
                    <th class="w-20 text-center">Stok</th>
                    <th class="w-48 text-center">Total</th>

                </tr>
            </thead>
        </table>
        
        <div class="overflow-y-auto mt-4" style="height: 500px;">
            <table class="min-w-full text-gray border-collapse">
                <colgroup>
                    <col style="width:1%">
                    <col style="width:6%">
                    <col style="width:8%">
                    <col style="width:auto%">
                    <col style="width:6%">
                    <col style="width:7%">
                    <col style="width:2%">
                    <col style="width:5%">
                </colgroup>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="p-2">
                            {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="text-center">{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>

                        <td class="text-center">
                            Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                        </td>

                        <td class="p-4 text-center">
                            Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            @if($product->stock <= 0)
                                <span class="text-red-500 font-bold">Habis</span>
                            @else
                                {{ $product->stock }}
                            @endif
                        </td>

                        <td class="text-center">
                            Rp {{ number_format($product->cost_price * $product->stock, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-4">
                            Data tidak ditemukan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>

    {{-- TOTAL --}}
    <div class="text-gray mt-4 font-bold text-lg">
        Total Nilai Seluruh Inventory:
        Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}
    </div>

</div>
</x-app-layout>
