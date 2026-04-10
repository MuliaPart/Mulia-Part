@forelse($products as $product)
 <div 
            x-data="{ open: false, editOpen: false }"
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
                    class="bg-green-600 text-white rounded hover:bg-green-700 px-3 py-2 text-sm">
                    Kelola Stok
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
                                class="bg-green-600 text-white rounded hover:bg-green-700 px-3 py-2 text-sm">
                                Kelola Stok
                            </button>

                            <form action="{{ route('cart.add') }}" method="POST" class="ml-auto" @click.stop>
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit"
                                    class="bg-indigo-600 text-white rounded hover:bg-indigo-700 px-4 py-2 transition">
                                    🛒 Tambah ke Keranjang
                                </button>
                            </form>

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

        </div>      

@empty
    <div class="col-span-5 text-center text-gray-500 py-10">
        Produk tidak ditemukan
    </div>
@endforelse