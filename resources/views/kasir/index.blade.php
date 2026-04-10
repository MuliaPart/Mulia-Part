<x-app-layout>

    <div class="p-4">

        <div class="flex gap-4 h-[85vh]">

            {{-- ====================================== --}}
            {{-- PANEL KIRI : LIST TRANSAKSI --}}
            {{-- ====================================== --}}
            <div class="w-1/2 bg-white rounded-xl shadow flex flex-col">
                {{-- HEADER --}}
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold">
                        Kasir
                    </h2>
                    <input type="text" name="customer_name" placeholder="Nama Pelanggan">
                    <select name="customer_type">
                        <option value="non_member">Non Member</option>
                        <option value="member">Member</option>
                    </select>
                    {{-- DELETE SEMUA --}}
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                            Delete Semua
                        </button>
                    </form>
                </div>
                                
                {{-- INPUT SCAN --}}
                <div id="scanForm" class="p-4">

                    <input
                        type="text"
                        id="scanCode"
                        name="code"
                        placeholder="Scan / ketik kode produk"
                        class="w-full border rounded-lg px-3 py-2 "
                        autocomplete="off"

                    >

                </div>

                {{-- LIST PRODUK --}}
                <div class="flex-1 overflow-y-auto p-4">

                    @php
                        $cart  = session('cart', []);
                        $total = 0;
                    @endphp

                    @if(empty($cart))

                        <div class="text-gray-400 text-center mt-20">
                            Belum ada produk
                        </div>
                    @else

                        <table class="w-full text-sm">

                            <thead class="border-b text-gray-600">

                                <tr>
                                    <th class="text-left py-2">Kode</th>
                                    <th class="text-left">Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Diskon</th>
                                    <th class="text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                @foreach($cart as $item)
                                    @php
                                        $discount = $item['discount'] ?? 0;
                                        $price = $item['price'];
                                        $qty = $item['qty'];

                                        if(str_contains($discount,'%')){
                                            $percent = (int) str_replace('%','',$discount);
                                            $discount = ($price * $qty) * $percent / 100;
                                        }

                                        $subtotal = ($price * $qty) - $discount;

                                        $total += $subtotal;

                                    @endphp
                                    <tr class="border-b">
                                        {{-- KODE PRODUK --}}
                                        <td class="py-2">
                                            {{ $item['code'] ?? '-' }}
                                        </td>
                                        {{-- NAMA --}}
                                        <td>
                                            {{ $item['name'] ?? '' }}
                                        </td>
                                        {{-- QTY --}}
                                        <td>
                                            <div class="flex items-center gap-1">

                                                {{-- TOMBOL MINUS --}}
                                                <button 
                                                    type="button"
                                                    class="qty-minus bg-gray-200 px-2 rounded"
                                                    data-id="{{ $item['id'] }}">
                                                    -
                                                </button>

                                                {{-- INPUT QTY --}}
                                                <input 
                                                    value="{{ $item['qty'] }}"
                                                    min="1"
                                                    class="qty-input w-10 text-center border rounded"
                                                    data-id="{{ $item['id'] }}"
                                                >

                                                {{-- TOMBOL PLUS --}}
                                                <button 
                                                    type="button"
                                                    class="qty-plus bg-gray-200 px-2 rounded"
                                                    data-id="{{ $item['id'] }}">
                                                    +
                                                </button>

                                            </div>

                                        </td>
                                        <td>
                                            <input 
                                                type="text"
                                                value="{{ $item['discount'] ?? 0 }}"
                                                class="discount-input w-16 text-center border rounded"
                                                data-id="{{ $item['id'] }}"
                                                placeholder="0 / 10%">

                                        </td>
                                        {{-- HARGA --}}
                                        <td class="text-right text-base">
                                            {{ number_format($subtotal) }}
                                        </td>
                                        {{-- DELETE --}}
                                        <td class="text-center">
                                            <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                                    X
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                {{-- DISKON TOTAL --}}
                <div class=" grid md:grid-cols-2 border-t flex w-full p-2">
                    <div class="flex">
                        <p class="p-2">Diskon</p>
                        <input id="totalDiscount" name="total_discount" value="0">
                    </div>
                    <div class="flex justify-end">
                        <p class="p-2">Metode Bayar</p>
                        <select name="payment_method">

                            <option value="CASH">CASH</option>
                            <option value="TRANSFER">TRANSFER/QRIS</option>
                            <option value="HUTANG">HUTANG</option>

                        </select>
                    </div>
                </div>
                {{-- TOTAL + BAYAR --}}
                <div class="border-t p-4">
                    <div class="flex justify-between text-lg font-bold mb-3">
                        <span>Total</span>
                        <span id="totalPrice" class="text-green-600">
                            Rp {{ number_format($total) }}
                        </span>
                    </div>
                    <input type="hidden" id="finalTotalInput">
                    {{-- TOMBOL BAYAR --}}
                    <button
                        type="button"
                        onclick="bayar(event)"
                        class="w-full bg-green-600 text-white px-6 py-3 rounded-lg text-lg">
                        Bayar
                    </button>
                </div>
            </div>
            {{-- ====================================== --}}
            {{-- PANEL KANAN : GAMBAR PRODUK --}}
            {{-- ====================================== --}}
            <div class="w-1/2 bg-white rounded-xl shadow p-4 overflow-y-auto">
                @if(empty($cart))
                    <div class="text-gray-400 text-center mt-20">
                        Belum ada produk
                    </div>
                @else
                    <div id="cartImages" class="grid grid-cols-3 gap-4">
                        @foreach($cart as $item)
                            <div class="border rounded-lg p-2 text-center">
                                <div class="flex justify-end text-base text-gray-500">
                                    {{ $item['code'] }}
                                </div>
                                <img
                                    src="{{ asset('storage/'.$item['image']) }}"
                                    class="h-32 object-contain mx-auto"
                                >
                                <div class="text-sm mt-2 font-semibold">
                                    {{ $item['name'] }}
                                </div>
                                
                                <div class="text-gray-800 text-lg font-semibold mt-2">
                                    Rp {{ number_format ($item['price']) }} x {{ $item['qty'] ?? 0 }}
                                </div>
                                
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- POPUP SUKSES -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-xl p-6 w-[400px] text-center shadow-lg">

            <h2 class="text-xl font-bold text-green-600 mb-3">
                Transaksi Berhasil ✅
            </h2>

            <p class="mb-2">Invoice:</p>
            <h3 id="invoiceText" class="font-bold text-lg mb-4"></h3>

            <div class="flex gap-3 justify-center">

                <button 
                    onclick="closeModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded">
                    Selesai
                </button>

                <button 
                    onclick="printNota()"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                    Cetak Nota
                </button>

            </div>

        </div>

    </div>
</x-app-layout>
@push('scripts')
<script>
    console.log('SCRIPT HIDUP');
    let cart = []; // 🔥 global biar aman

    document.addEventListener("DOMContentLoaded", function(){

        cart = @json(session('cart', []));
        renderCart(cart);

        const form  = document.getElementById('scanForm');
        const input = document.getElementById('scanCode');

        if(form){
            form.addEventListener('submit', function(e){

                e.preventDefault();

                fetch("{{ route('cart.add.code') }}", {
                    method: "POST",
                    headers:{
                        "Content-Type":"application/json",
                        "X-CSRF-TOKEN":"{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: input.value
                    })
                })
                .then(res => res.json())
                .then(data => {

                    if(data.error){
                        alert(data.error);
                        return;
                    }

                    if(data.success){
                        cart = data.cart;
                        renderCart(cart);
                    }

                });

                input.value = '';
                input.focus();

            });
        }

        window.loadCart = function(){
            fetch("{{ route('cart.get') }}")
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart(cart);
                });
        }

    });


    function renderCart(cart){

        const table  = document.getElementById('cartItems');
        const images = document.getElementById('cartImages');
        const total  = document.getElementById('totalPrice');

        if(table) table.innerHTML = '';
        if(images) images.innerHTML = '';

        let totalPrice = 0;

        Object.values(cart).forEach(item => {

            let price = item.sell_price ?? item.price ?? 0;
            let discount = item.discount ?? 0;

            if(typeof discount === "string" && discount.includes('%')){
                let percent = parseInt(discount.replace('%',''));
                discount = (price * item.qty) * percent / 100;
            }

            let subtotal = (price * item.qty) - discount;
            totalPrice += subtotal;

            if(table){
                table.innerHTML += `
                    <tr class="border-b">
                        <td class="py-2">${item.code}</td>
                        <td>${item.name}</td>

                        <td>
                            <div class="flex items-center gap-1">
                                <button type="button" class="qty-minus bg-gray-200 px-2 rounded" data-id="${item.id}">-</button>

                                <input value="${item.qty}" class="qty-input w-10 text-center border rounded" data-id="${item.id}">

                                <button type="button" class="qty-plus bg-gray-200 px-2 rounded" data-id="${item.id}">+</button>
                            </div>
                        </td>

                        <td class="text-center">
                            <input type="text" value="${item.discount ?? 0}" data-id="${item.id}" class="discount-input border rounded px-2 py-1 w-20 text-center">
                        </td>

                        <td class="text-right">
                            ${formatRupiah(subtotal)}
                        </td>
                    </tr>
                `;
            }

            if(images){
                images.innerHTML += `
                    <div class="border rounded-lg p-2 text-center">
                        <div class="flex justify-end text-gray-500">${item.code}</div>

                        <img src="/storage/${item.image}" class="h-32 mx-auto">

                        <div class="font-semibold mt-2">${item.name}</div>

                        <div class="mt-2">
                            ${formatRupiah(item.price)} x ${item.qty}
                        </div>
                    </div>
                `;
            }

        });

        let totalDiscountInput = document.getElementById('totalDiscount');
        let totalDiscount = 0;

        if(totalDiscountInput){
            let value = totalDiscountInput.value;

            if(typeof value === "string" && value.includes('%')){
                let percent = parseInt(value.replace('%',''));
                totalDiscount = totalPrice * percent / 100;
            }else{
                totalDiscount = parseInt(value) || 0;
            }
        }

        let finalTotal = totalPrice - totalDiscount;
        if(finalTotal < 0) finalTotal = 0;

        if(total){
            total.innerText = formatRupiah(finalTotal);
        }

        let finalInput = document.getElementById('finalTotalInput');
        if(finalInput){
            finalInput.value = finalTotal;
        }
    }


    function formatRupiah(angka){
        return "Rp " + angka.toLocaleString('id-ID');
    }


    function updateQty(id, qty){

        fetch("{{ route('cart.update.qty') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id, qty })
        })
        .then(res => res.json())
        .then(() => loadCart());

    }


    document.addEventListener('click', function(e){

        if(e.target.classList.contains('qty-plus')){
            let id = e.target.dataset.id;
            let input = document.querySelector('.qty-input[data-id="'+id+'"]');
            updateQty(id, parseInt(input.value) + 1);
        }

        if(e.target.classList.contains('qty-minus')){
            let id = e.target.dataset.id;
            let input = document.querySelector('.qty-input[data-id="'+id+'"]');
            let qty = parseInt(input.value) - 1;
            if(qty < 1) qty = 1;
            updateQty(id, qty);
        }

    });


    document.addEventListener('change', function(e){

        if(e.target.classList.contains('qty-input')){
            fetch("{{ route('cart.update.qty') }}",{
                method:"POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                },
                body:JSON.stringify({
                    id:e.target.dataset.id,
                    qty:e.target.value
                })
            }).then(()=>loadCart());
        }

        if(e.target.classList.contains('discount-input')){
            fetch("{{ route('cart.update.discount') }}",{
                method:"POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                },
                body:JSON.stringify({
                    id:e.target.dataset.id,
                    discount:e.target.value
                })
            }).then(()=>loadCart());
        }

    });


    window.bayar = function(){

        let totalDiscount = document.getElementById('totalDiscount').value;
        let paymentMethod = document.querySelector('[name="payment_method"]').value;
        let finalTotal    = document.getElementById('finalTotalInput').value;
        let customerName  = document.querySelector('[name="customer_name"]').value;
        let customerType  = document.querySelector('[name="customer_type"]').value;

        fetch("{{ route('kasir.bayar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                customer_name: customerName,
                customer_type: customerType,
                total_discount: totalDiscount,
                payment_method: paymentMethod,
                final_total: finalTotal
            })
        })
        .then(res => res.json())
        .then(res => {

            console.log('HASIL:', res);

            if(res.success){

                document.getElementById('invoiceText').innerText = res.invoice;
                window.lastTransactionId = res.transaction_id;

                document.getElementById('successModal').classList.remove('hidden');

                loadCart();

            } else {
                alert(res.message || 'Gagal bayar');
            }

        })
        .catch(err => {
            console.log(err);
            alert('Terjadi error saat bayar');
        });

    }


    function closeModal(){
        document.getElementById('successModal').classList.add('hidden');
        loadCart();
    }

    function printNota(){
        if(!window.lastTransactionId){
            alert('Transaksi tidak ditemukan');
            return;
        }
        window.open(`/nota/${window.lastTransactionId}`, '_blank');
    }
</script>
@endpush
