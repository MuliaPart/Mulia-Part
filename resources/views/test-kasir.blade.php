<x-app-layout>
    
    @if(session('error'))
        <div style="background:red;color:white;padding:10px;margin:10px">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="background:green;color:white;padding:10px;margin:10px">
            {{ session('success') }}
        </div>
    @endif
    <div class="p-6">

        <div class="flex gap-4 h-[85vh]">

            <!-- ================================= -->
            <!-- PANEL KIRI : TRANSAKSI -->
            <!-- ================================= -->

            <div class="w-1/2 bg-white rounded-xl shadow flex flex-col">

                <!-- HEADER -->

                <div class="p-4 border-b flex justify-between items-center">

                    <h2 class="text-xl font-bold">
                        Pembayaran
                    </h2>

                    <form
                        method="POST"
                        action="{{ route('cart.clear') }}"
                    >
                        @csrf

                        <button
                            class="bg-red-500 text-white px-3 py-1 rounded"
                        >
                            Delete Semua
                        </button>

                    </form>

                </div>



                <!-- SCAN BARANG -->

                <div
                    id="scanForm"
                    class="p-4"
                >

                    <input
                        id="scanCode"
                        class="w-full border rounded px-3 py-2"
                        placeholder="Scan / input kode barang"
                        autocomplete="off"
                        autofocus
                    >

                </div>



                <!-- LIST CART -->

                <div class="flex-1 overflow-y-auto p-4">

                    <table class="w-full text-sm">

                        <thead class="border-b">

                            <tr>

                                <th class="text-left py-2">
                                    Kode
                                </th>

                                <th class="text-left">
                                    Produk
                                </th>

                                <th class="text-left">
                                    Qty
                                </th>

                                <th class="text-left">
                                    Harga
                                </th>

                                <th class="text-center">
                                    Diskon
                                </th>

                                <th class="text-right">
                                    Subtotal
                                </th>

                                <th></th>

                            </tr>

                        </thead>

                        <tbody id="cartItems"></tbody>

                    </table>

                </div>



                <!-- DISKON TOTAL -->

                <div class="border-t p-4">

                    <div class="flex items-center gap-3">

                        <span class="text-sm font-semibold">
                            Diskon Total
                        </span>

                        <input
                            id="totalDiscount"
                            class="border rounded px-2 py-1 w-24 text-center"
                            value="0"
                        >

                    </div>
                        <input
                            type="hidden"
                            name="total_discount"
                            id="finalDiscountInput"
                        >
                </div>



                <!-- TOTAL + BAYAR -->

                <div class="border-t p-4">

                    <div class="flex justify-between text-lg font-bold mb-4">

                        <span>
                            Total
                        </span>

                        <span
                            id="totalPrice"
                            class="text-green-600"
                        >
                            Rp 0
                        </span>

                    </div>
                    <input type="hidden" id="finalTotalInput">
                    <div
                        method="POST"
                        action="{{ route('kasir.bayar') }}"
                        class="grid grid-cols-4 gap-2 items-end"
                    >
                        @csrf

                        <input
                            type="text"
                            name="customer_name"
                            placeholder="Nama pelanggan"
                            class="border rounded px-2 py-2 w-full"
                        >

                        <select
                            name="customer_type"
                            class="border rounded px-2 py-2 w-full"
                        >
                            <option value="non_member">Non Member</option>
                            <option value="member">Member</option>
                        </select>

                        <select
                            name="payment_method"
                            class="border rounded px-2 py-2 w-full"
                        >
                            <option value="CASH">CASH</option>
                            <option value="TRANSFER">Transfer / QRIS</option>
                            <option value="HUTANG">HUTANG</option>
                        </select>


                    
                    </div>
                    <p class="p-2">Keterangan</p>
                    <textarea
                        name="note"
                        id="noteInput"
                        placeholder="Keterangan (opsional)"
                        class="border rounded px-2 py-2 w-full col-span-4"
                    ></textarea>
                    {{-- TOMBOL BAYAR --}}
                    <button
                        type="button"
                        class="bg-green-600 text-white py-2 rounded-lg w-full mt-2"
                        onclick="bayar(event)"
                        >
                        Bayar
                    </button>
                </div>
            </div>



            <!-- ================================= -->
            <!-- PANEL KANAN : GAMBAR -->
            <!-- ================================= -->
            <div class="w-1/2 bg-white rounded-xl shadow p-4 overflow-y-auto">
                <div id="cartImages" class="grid grid-cols-3 gap-4">
                    <div class="text-center col-span-3 mt-20">
                        Belum ada produk
                    </div>
                </div>
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

    <script>

    console.log("SCRIPT KASIR FINAL LOADED")

    let cart = []

    /* ======================================
    LOAD CART
    ====================================== */
    function loadCart(){
        fetch("{{ route('cart.get') }}")
            .then(res => res.json())
            .then(data => {
                cart = data.cart
                renderCart(cart)
            })
    }
    loadCart()


    /* ======================================
    SCAN BARANG
    ====================================== */
    const input = document.getElementById('scanCode')

    input.addEventListener('keypress', function(e){
        if(e.key === 'Enter'){
            e.preventDefault()

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

            })

            input.value = ''
        }
    })


    /* ======================================
    RENDER CART
    ====================================== */
    function renderCart(cart){

        const table  = document.getElementById('cartItems')
        const images = document.getElementById('cartImages')
        const total  = document.getElementById('totalPrice')

        if(table) table.innerHTML = ''
        if(images) images.innerHTML = ''

        let totalPrice = 0

        cart.forEach(item => {

            let price = item.sell_price ?? item.price ?? 0
            let discount = item.discount ?? 0

            if(typeof discount === "string" && discount.includes('%')){
                let percent = parseInt(discount.replace('%',''))
                discount = (price * item.qty) * percent / 100
            }

            let subtotal = (price * item.qty) - discount
            totalPrice += subtotal

            if(table){
                table.innerHTML += `
                    <tr class="border-b">
                        <td>${item.code}</td>
                        <td class="max-w-[350px] truncate">
                            ${item.name}
                        </td>

                        <td>
                            <button class="qty-minus px-2" data-id="${item.id}" data-qty="${item.qty}">-</button>
                            <span class="qty-value">${item.qty}</span>
                            <button class="qty-plus px-2" data-id="${item.id}" data-qty="${item.qty}">+</button>
                        </td>
                        <td>${item.price}</td>
                        <td>
                            <input 
                                class="discount-input border w-24 text-center"
                                data-id="${item.id}"
                                value="${item.discount ?? 0}"
                                onclick="this.select()"
                            >
                        </td>

                        <td class="item-subtotal text-right" data-value="${subtotal}">
                            ${formatRupiah(subtotal)}
                        </td>

                        <td>
                            <button class="btn-delete bg-red-500 text-white px-2" data-id="${item.id}">X</button>
                        </td>
                    </tr>
                `
            }

            if(images){
                if(images){

                if(cart.length === 0){
                    images.innerHTML = `
                        <div class="text-gray-400 text-center col-span-3 mt-20">
                            Belum ada produk
                        </div>
                    `;
                }
                else{

                    images.innerHTML += `
                        <div class="border rounded-lg p-2 text-center">
                            <div class="flex justify-end text-gray-500">${item.code}</div>

                            <img src="/storage/${item.image}" class="h-32 w-full object-contain mx-auto">

                            <div class="font-semibold mt-2 break-words text-sm leading-tight">
                                ${item.name}
                            </div>

                            <div class="mt-2">
                                <h class="text-green">${formatRupiah(item.price)}</h> x ${item.qty}
                            </div>
                        </div>
                    `;
                }
            }
        }

    })

        document.getElementById('finalTotalInput').value = totalPrice
        if(total) total.innerText = formatRupiah(totalPrice)
    }


    /* ======================================
    UPDATE QTY
    ====================================== */
    function updateQty(id, qty){
        fetch("{{ route('cart.update.qty') }}", {
            method: "POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },
            body: JSON.stringify({ id, qty })
        })
        .then(res => res.json())
        .then(() => loadCart())
    }


    /* ======================================
    CLICK EVENT
    ====================================== */
    document.addEventListener('click', function(e){

        if(e.target.classList.contains('qty-plus')){
            let id = e.target.dataset.id

            let row = e.target.closest('tr')
            let currentQty = parseInt(row.querySelector('.qty-value').innerText)

            let qty = currentQty + 1

            updateQty(id, qty)
        }

        if(e.target.classList.contains('qty-minus')){
            let id = e.target.dataset.id

            let row = e.target.closest('tr')
            let currentQty = parseInt(row.querySelector('.qty-value').innerText)

            let qty = currentQty - 1
            if(qty < 1) qty = 1

            updateQty(id, qty)
        }

        if(e.target.classList.contains('btn-delete')){
            let id = e.target.dataset.id

            fetch(`/cart/remove/${id}`, {
                method: "POST",
                headers:{
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                }
            })
            .then(() => loadCart())
        }

    })
        /* ======================================
    DISKON PER ITEM
    ====================================== */
        document.addEventListener('change', function(e){

            if(e.target.classList.contains('discount-input')){

                let id = e.target.dataset.id
                let discount = e.target.value

                fetch("{{ route('cart.update.discount') }}", {
                    method : "POST",
                    headers : {
                        "Content-Type":"application/json",
                        "X-CSRF-TOKEN":"{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id:id,
                        discount:discount
                    })
                })
                .then(res=>res.json())
                .then(()=>loadCart())

            }

        }, true)


    /* ======================================
    FORMAT RUPIAH
    ====================================== */
    function formatRupiah(angka){
        return "Rp " + angka.toLocaleString('id-ID')
    }


    /* ======================================
    🔥 BAYAR (INI YANG PENTING)
    ====================================== */
    window.bayar = function(e){

        e.preventDefault()

        console.log("TOMBOL BAYAR DIKLIK") // DEBUG

        let totalDiscount = document.getElementById('totalDiscount').value
        let paymentMethod = document.querySelector('[name="payment_method"]').value
        let finalTotal    = document.getElementById('finalTotalInput').value
        let customerName  = document.querySelector('[name="customer_name"]').value
        let customerType  = document.querySelector('[name="customer_type"]').value
        let note = document.getElementById('noteInput').value

        fetch("{{ route('kasir.bayar') }}", {
            method: "POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },
            body: JSON.stringify({
                customer_name: customerName,
                customer_type: customerType,
                total_discount: totalDiscount,
                payment_method: paymentMethod,
                final_total: finalTotal,
                note : note,
            })
        })
        .then(res => res.json())
        .then(res => {

            console.log("HASIL BAYAR:", res)

            if(res.success){

                document.getElementById('invoiceText').innerText = res.invoice

                window.lastTransactionId = res.transaction_id

                document.getElementById('successModal').classList.remove('hidden')

                loadCart()

            }else{
                alert("Gagal bayar")
            }

        })
        .catch(err => {
            console.log(err)
            alert("ERROR BAYAR")
        })
    }


    /* ======================================
    MODAL
    ====================================== */
    function closeModal(){
        document.getElementById('successModal').classList.add('hidden')
    }

    function printNota(){

        if(!window.lastTransactionId){
            alert('Transaksi tidak ditemukan')
            return;
        }

        window.open('/nota/' + window.lastTransactionId, '_blank');
    }
    /* ======================================
    TOTAL DISKON
    ====================================== */
    document.getElementById('totalDiscount').addEventListener('input', function(){

    let subtotal = 0

        document.querySelectorAll('.item-subtotal').forEach(el => {
            subtotal += parseInt(el.dataset.value) || 0
        })

        let val = this.value
        let discount = 0

        if(val.includes('%')){
            let percent = parseInt(val.replace('%','')) || 0
            discount = subtotal * percent / 100
        }else{
            discount = parseInt(val) || 0
        }

        document.getElementById('finalDiscountInput').value = discount

        let finalTotal = subtotal - discount
        if(finalTotal < 0) finalTotal = 0

        document.getElementById('totalPrice').innerText = formatRupiah(finalTotal)
        document.getElementById('finalTotalInput').value = finalTotal
    })

    </script>
</x-app-layout>