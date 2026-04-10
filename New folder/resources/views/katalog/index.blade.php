<x-app-layout>

<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-4">
        <h2 class="md:col-span-1 text-4xl font-bold flex justify-center">Daftar Produk</h2>
        <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-6">
            <div class="md:col-span-4">
                <form method="GET" action="{{ route('katalog') }}" class="mb-3 p-3">
                    <input type="text" 
                        id="search" 
                        placeholder="Cari nama/kode produk..."
                        class="md:col-span-2 border rounded-l px-8 py-2 w-full  focus:outline-none focus:ring focus:ring-blue-200">
                </form>
            </div>
            <!-- Filter Kategori -->
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-6">
                <select id="category" 
                    class="md:col-span-5 mb-6 mt-3 w-full focus:outline-none focus:ring focus:ring-blue-200 flex justify-start">
                        
                        <option value="">Semua Kategori</option>

                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->name }}
                            </option>
                        @endforeach
                </select>
                <a href="{{ route('katalog') }}" 
                        class="md:col-span-1 group flex items-center bg-gray-400 text-white px-1 py-1 ml-2 mr-6 mb-6 mt-3 rounded hover:bg-gray-500 transition">

                        <svg xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor" 
                        class="size-2 w-7 h-7 transition-transform duration-300 group-hover:rotate-180">
                        <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                </a>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div id="product-list" class="grid grid-cols-1 md:grid-cols-5 bg-white shadow p-3">
        @foreach($products as $product)
        <div 
            x-data="{ open:false, editOpen:false, restockOpen:false }"
            class="bg-white shadow rounded-xl p-4 hover:shadow-lg transition mt-3 ml-2 relative"
        >

            <!-- AREA YANG BISA DIKLIK -->
            <div @click="open = true" class="cursor-pointer">

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <p class="text-gray-700 text-sm">
                        {{ $product->category->name ?? '-' }}
                    </p>
                    <span class="text-gray-700 text-sm flex justify-end">
                        {{ $product->code }}
                    </span>
                </div>

                <div class="w-full h-56 bg-white flex items-center justify-center rounded overflow-hidden">

                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" 
                            class="max-w-full max-h-full object-contain hover:scale-110 transition duration-300">
                    @else
                        <img src="{{ asset('images/no-image.png') }}"
                            class="max-w-full max-h-full object-contain">
                    @endif

                </div>

                <h3 class="font-semibold">
                    {{ $product->name }}
                </h3>

                <h3 class="ml-3 grid grid-cols-1 md:grid-cols-2">
                    Pokok
                    <p class="text-green-600 font-bold">
                        : Rp {{ number_format($product->cost_price,0,',','.') }}
                    </p>
                    Jual
                    <p class="text-green-600 font-bold">
                        : Rp {{ number_format($product->sell_price,0,',','.') }}
                    </p>
                </h3>

                @if($product->stock > 0)
                    <p class="text-sm mt-1 text-green-600">
                        Stok: {{ $product->stock }}
                    </p>
                @else
                    <p class="text-sm mt-1 text-red-600 font-semibold">
                        Stok Habis
                    </p>
                @endif
            </div>

            <!-- BUTTON AREA -->
            <div class="flex flex-wrap items-center gap-3 mt-3">

                <button 
                    type="button"
                    @click.stop="editOpen = true"
                    class="bg-blue-600 text-white rounded hover:bg-blue-700 px-3 py-2 text-sm">
                    Edit Produk
                </button>

                <button 
                    type="button"
                    @click.stop="restockOpen = true"
                    class="bg-green-600 text-white rounded hover:bg-green-700 px-3 py-2 text-sm">
                    Restok
                </button>

                <button type="submit"
                    class="add-to-cart bg-indigo-600 text-white rounded hover:bg-indigo-700 px-3 py-2 ml-auto"
                    data-id="{{ $product->id }}">
                    🛒 
                </button>

            </div>

            <!-- ============================= -->
            <!-- MODAL DETAIL PRODUK -->
            <!-- ============================= -->

            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="open = false"
                x-effect="document.body.classList.toggle('overflow-hidden', open)"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >

                <div 
                    class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    @click="open = false"
                ></div>

                <div 
                    @click.away="open = false"
                    x-transition
                    x-transition:enter="transform transition ease-out duration-300" 
                    x-transition:enter-start="opacity-0 scale-90 translate-y-10" 
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                    x-transition:leave="transform transition ease-in duration-200" 
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                    x-transition:leave-end="opacity-0 scale-90 translate-y-10" 
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl p-8"
                >

                    <button 
                        @click="open = false"
                        class="absolute top-4 right-6 text-gray-500 hover:text-red-500 text-3xl">
                        ✕
                    </button>

                    <div class="h-[70vh] bg-white flex items-center justify-center rounded-xl overflow-hidden group">

                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-150 cursor-zoom-in">
                        @endif
                    </div>

                    <!-- DETAIL -->
                    <div class="mt-6">
                        <h2 class="text-3xl font-bold">
                            {{ $product->name }}
                        </h2>

                        <p class="text-gray-500 mt-2">
                            {{ $product->category->name ?? '-' }}
                        </p>

                        <p class="mt-4 text-2xl font-bold text-green-600">
                            Rp {{ number_format($product->sell_price,0,',','.') }}
                        </p>

                        <!-- BUTTON AREA -->
                        <div class="flex flex-wrap items-center gap-3 mt-6">

                            <button 
                                type="button"
                                @click.stop="editOpen = true"
                                class="bg-blue-600 text-white rounded hover:bg-blue-700 px-3 py-2 text-sm">
                                Edit Produk
                            </button>

                            <button 
                                type="button"
                                @click.stop="restockOpen = true"
                                class="bg-green-600 text-white rounded hover:bg-green-700 px-3 py-2 text-sm">
                                Restok
                            </button>
                            <button type="submit"
                                class="add-to-cart bg-indigo-600 text-white rounded hover:bg-indigo-700 px-3 py-2 ml-auto"
                                data-id="{{ $product->id }}">
                                🛒 
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================= -->
            <!-- MODAL EDIT PRODUK -->
            <!-- ============================= -->

            <div 
                x-show="editOpen"
                @keydown.escape.window="editOpen = false"
                x-effect="document.body.classList.toggle('overflow-hidden', editOpen)"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >

                <div 
                    class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    @click="editOpen = false"
                ></div>

                <div 
                    @click.away="editOpen = false"
                    x-transition
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8"
                >

                    <button 
                        @click="editOpen = false"
                        class="absolute top-4 right-6 text-gray-500 hover:text-red-500 text-2xl">
                        ✕
                    </button>

                    <h2 class="text-2xl font-bold mb-6">
                        Edit Produk
                    </h2>

                    <form action="{{ route('products.update', $product) }}" 
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <!-- hidden input supaya validasi controller tidak gagal -->
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <input type="hidden" name="cost_price" value="{{ $product->cost_price }}">
                            <input type="hidden" name="sell_price" value="{{ $product->sell_price }}">
                            <input type="hidden" name="stock" value="{{ $product->stock }}">
                            <div>
                                Kategori
                                <select name="category_id" 
                                    class="w-full"> 
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm">Nama Produk</label>
                                <input type="text" name="name"
                                    value="{{ $product->name }}"
                                    class="w-full border rounded px-4 py-2">
                            </div>
                            <div>
                                <label class="text-sm">Harga Pokok</label>
                                <input type="number" name="cost_price"
                                    value="{{ $product->cost_price }}"
                                    class="w-full border rounded px-4 py-2">
                            </div>
                            <div>
                                <label class="text-sm">Harga Jual</label>
                                <input type="number" name="sell_price"
                                    value="{{ $product->sell_price }}"
                                    class="w-full border rounded px-4 py-2">
                            </div>

                            <div>
                                <label class="text-sm">Stok</label>
                                <input type="number" name="stock"
                                    value="{{ $product->stock }}"
                                    class="w-full border rounded px-4 py-2">
                            </div>
                            <div>
                                <label>Foto Produk</label>
                                <input type="file" name="image">
                            </div>

                           <div class="flex justify-end gap-3 pt-4">
                                <button 
                                    type="button"
                                    @click="editOpen = false"
                                    class="px-4 py-2 bg-gray-300 rounded">
                                    Batal
                                </button>

                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>  
                    </form>
                    
                </div>
            </div>
            <div 
                x-show="restockOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4">

                <div class="absolute inset-0 bg-black/60"
                    @click="restockOpen=false"></div>

                    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 relative">

                        <button @click="restockOpen=false"
                            class="absolute top-4 right-6 text-gray-500 hover:text-red-500 text-2xl">
                            ✕
                        </button>

                        <h2 class="text-xl font-bold mb-4">Restok Produk</h2>

                        <form action="{{ route('products.restock',$product) }}" method="POST">
                            @csrf

                                <div class="mb-3">
                                <label>Jumlah Restok *</label>
                                <input type="number" name="qty" required
                                class="w-full border rounded">
                                <p class="text-sm text-red-600 font-semibold">
                                    * Wajib diisi
                                </p>
                                </div>

                                <div class="mb-3">
                                <label>Harga Pokok</label>
                                <input type="number" name="cost_price"
                                class="w-full border rounded p-2">
                                </div>

                                <div class="mb-3">
                                <label>Harga Jual</label>
                                <input type="number" name="sell_price"
                                class="w-full border rounded p-2">
                                </div>

                                <div class="mb-3">
                                <label>Supplier</label>
                                <input type="text" name="supplier"
                                class="w-full border rounded p-2">
                                </div>

                                <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded">
                                Simpan Restok
                                </button>

                        </form>

                </div>
            </div>

        </div>
        @endforeach
    </div>
    <div class="mt-6">
    {{ $products->links() }}
    </div>
    
</div>

<!-- 🔥 SCRIPT LIVE SEARCH -->
<script>
    document.getElementById('search').addEventListener('keyup', filterProducts);
    document.getElementById('category').addEventListener('change', filterProducts);

    // cegah enter reload
    document.getElementById('search').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });

    function filterProducts() {
        let search = document.getElementById('search').value;
        let category = document.getElementById('category').value;

        fetch(`{{ route('katalog') }}?search=${search}&category=${category}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('product-list').innerHTML = data;
        });
    }


        document.addEventListener('click', function(e){

            if(e.target.classList.contains('add-to-cart')){

                let productId = e.target.dataset.id;

                fetch("{{ route('cart.add') }}", {

                    method: "POST",

                    headers: {

                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"

                    },

                    body: JSON.stringify({

                        product_id: productId

                    })

                })
                .then(res => res.json())
                .then(data => {

                    console.log(data);
                    alert("Produk masuk keranjang");

                });

            }

        });

    </script>

</x-app-layout>