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

            <button 
                onclick="openCreateModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                + Tambah Produk
            </button>
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
                <form method="POST" action="{{ route('products.deleteAll') }}"
                    onsubmit="return confirm('Yakin hapus semua produk?')">
                    @csrf
                    <button class="bg-red-500 text-white px-3 py-2 rounded">
                        Delete All
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
            <span class="text-blue-500">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }} </span>
            Total Nilai Jual Keseluruhan:
            <span class="text-green-600">Rp {{ number_format($totalSellingValue, 0, ',', '.') }} </span>
        </div>

    </div>
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-[500px] shadow-lg relative">

        <h2 class="text-xl font-bold mb-4">Tambah Produk</h2>

        <button 
            onclick="closeCreateModal()"
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl">
            ✕
        </button>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- NAMA -->
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>

            <!-- KATEGORI -->
            <div class="mb-3">
                <label>Kategori</label>
                <select id="categorySelect" name="category_id" class="w-full border rounded p-2" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- KODE OTOMATIS -->
            <div class="mb-3">
                <label>Kode Produk</label>
                <input 
                    type="text" 
                    id="productCode" 
                    name="code"
                    class="w-full border rounded p-2 bg-gray-100"
                    readonly
                    required
                >
            </div>

            <!-- HARGA POKOK -->
            <div class="mb-3">
                <label>Harga Pokok</label>
                <input type="number" name="cost_price" class="w-full border rounded p-2" required>
            </div>

            <!-- HARGA JUAL -->
            <div class="mb-3">
                <label>Harga Jual</label>
                <input type="number" name="sell_price" class="w-full border rounded p-2" required>
            </div>

            <!-- STOK -->
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stock" class="w-full border rounded p-2" required>
            </div>

            <!-- GAMBAR -->
            <div class="mb-3">
                <label>Gambar</label>
                <input type="file" name="image" class="w-full">
            </div>
            
            <button class="bg-green-600 text-white px-4 py-2 rounded w-full">
                Simpan
            </button>
        </form>

    </div>

</div>

<script>

    function closeCreateModal(){
        document.getElementById('createModal').classList.add('hidden');
    }

    /* ======================================
    AUTO GENERATE KODE
    ====================================== */
    document.getElementById('categorySelect').addEventListener('change', function(){

        let categoryId = this.value;

        if(!categoryId){
            document.getElementById('productCode').value = '';
            return;
        }

        fetch(`/generate-code/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                console.log('KODE BARU:', data.code);

                document.getElementById('productCode').value = data.code;
            })
            .catch(err => {
                console.error('ERROR GENERATE CODE:', err);
            });

    });


    function openCreateModal(){
        document.getElementById('createModal').classList.remove('hidden')
    }

    function closeCreateModal(){
        document.getElementById('createModal').classList.add('hidden')
    }
    </script>
</x-app-layout>
