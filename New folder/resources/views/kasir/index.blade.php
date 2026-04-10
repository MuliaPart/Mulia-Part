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
                <form id="scanForm" class="p-4">

                    <input
                        type="text"
                        id="scanCode"
                        name="code"
                        placeholder="Scan / ketik kode produk"
                        class="w-full border rounded-lg px-3 py-2 "
                        autocomplete="off"

                    >

                </form>

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

                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer/QRIS</option>
                            <option value="hutang">Hutang</option>

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
                    {{-- TOMBOL BAYAR --}}
                    <form action="{{ route('kasir.bayar') }}" method="POST" id="bayarForm">
                        @csrf
                        <input type="hidden" name="final_total" id="finalTotalInput">
                        <button
                            type="submit"
                            class="w-full bg-green-600 text-white px-6 py-3 rounded-lg text-lg">
                            Bayar
                        </button>
                    </form>
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


    <script>
       let cart = @json(session('cart', []));
renderCart(cart);

function loadCart(){
    fetch("{{ route('cart.get') }}")
        .then(res => res.json())
        .then(data => {
            renderCart(data.cart)
        });
}

const form  = document.getElementById('scanForm');
const input = document.getElementById('scanCode');

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
            alert(data.error)
            return
        }

        if(data.success){
            cart = data.cart
            renderCart(cart)
        }

    });

    input.value = '';
    input.focus();

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

                            <button 
                                type="button"
                                class="qty-minus bg-gray-200 px-2 rounded"
                                data-id="${item.id}">
                                -
                            </button>

                            <input 
                                value="${item.qty}"
                                min="1"
                                class="qty-input w-10 text-center border rounded"
                                data-id="${item.id}"
                            >

                            <button 
                                type="button"
                                class="qty-plus bg-gray-200 px-2 rounded"
                                data-id="${item.id}">
                                +
                            </button>

                        </div>
                    </td>

                    <td class="text-center">
                        <input 
                            type="text"
                            value="${item.discount ?? 0}"
                            data-id="${item.id}"
                            class="discount-input border rounded px-2 py-1 w-20 text-center"
                        >
                    </td>

                    <td class="text-right">
                        ${formatRupiah(subtotal)}
                    </td>

                    <td class="text-center">
                        <button class="bg-red-500 text-white px-3 py-1 rounded">
                            X
                        </button>
                    </td>

                </tr>
            `;
        }

        if(images){
            images.innerHTML += `
                <div class="border rounded-lg p-2 text-center">

                    <div class="flex justify-end text-base text-gray-500">
                        ${item.code}
                    </div>

                    <img 
                        src="/storage/${item.image}"
                        class="h-32 object-contain mx-auto"
                    >

                    <div class="text-sm mt-2 font-semibold">
                        ${item.name}
                    </div>

                    <div class="text-gray-800 text-lg font-semibold mt-2">
                        ${formatRupiah(item.price)} x ${item.qty ?? 0}
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

    if(finalTotal < 0){
        finalTotal = 0;
    }

    if(total){
        total.innerText = formatRupiah(finalTotal);
    }

    let finalInput = document.getElementById('finalTotalInput');

    if(finalInput){
        finalInput.value = finalTotal;
    }

}
document.getElementById('totalDiscount').addEventListener('input', function(){

    renderCart(cart);

});

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

        body: JSON.stringify({
            id: id,
            qty: qty
        })

    })
    .then(res => res.json())
    .then(data => {

        loadCart();

    });

}


document.addEventListener('click', function(e){

    // tombol +
    if(e.target.classList.contains('qty-plus')){

        let id = e.target.dataset.id;

        let input = document.querySelector('.qty-input[data-id="'+id+'"]');

        let qty = parseInt(input.value) + 1;

        updateQty(id, qty);

    }

    // tombol -
    if(e.target.classList.contains('qty-minus')){

        let id = e.target.dataset.id;

        let input = document.querySelector('.qty-input[data-id="'+id+'"]');

        let qty = parseInt(input.value) - 1;

        if(qty < 1) qty = 1;

        updateQty(id, qty);

    }

});


document.addEventListener('change', function(e){

    /* UPDATE QTY */

    if(e.target.classList.contains('qty-input')){

        let id = e.target.dataset.id;
        let qty = e.target.value;

        fetch("{{ route('cart.update.qty') }}",{

            method:"POST",

            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },

            body:JSON.stringify({
                id:id,
                qty:qty
            })

        })
        .then(res=>res.json())
        .then(data=>{
            loadCart();
        });

    }

    /* UPDATE DISKON */

    if(e.target.classList.contains('discount-input')){

        let id = e.target.dataset.id;
        let discount = e.target.value;

        fetch("{{ route('cart.update.discount') }}",{

            method:"POST",

            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },

            body:JSON.stringify({
                id:id,
                discount:discount
            })

        })
        .then(res=>res.json())
        .then(data=>{
            loadCart();
        });

    }

});

           <div class="border rounded-lg p-2 text-center">

                <div class="flex justify-end text-gray-500">
                    ${item.code}
                </div>

                <img
                    src="/storage/${item.image}"
                    class="h-28 object-contain mx-auto"
                >

                <div class="text-sm font-semibold mt-2">
                    ${item.name}
                </div>

                <div class="text-green-600 font-bold">
                    <h class="text-greem-600">${formatRupiah(item.price)}</h> <h class="text-gray-800"> x${item.qty}</h>
                </div>
            </div>
</script>

</x-app-layout>