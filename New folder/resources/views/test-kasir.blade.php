<x-app-layout>

    <div class="p-6">

        <div class="flex gap-4 h-[85vh]">

            <!-- ================================= -->
            <!-- PANEL KIRI : TRANSAKSI -->
            <!-- ================================= -->

            <div class="w-1/2 bg-white rounded-xl shadow flex flex-col">

                <!-- HEADER -->

                <div class="p-4 border-b flex justify-between items-center">

                    <h2 class="text-xl font-bold">
                        Kasir Cadangan
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

                <form
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

                </form>



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

                                <th class="text-center">
                                    Qty
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



                    <form
                        method="POST"
                        action="{{ route('kasir.bayar') }}"
                    >

                        @csrf


                        <input
                            type="text"
                            name="customer_name"
                            placeholder="Nama pelanggan"
                            class="border rounded px-2 py-1 w-full mb-2"
                        >


                        <select
                            name="customer_type"
                            class="border rounded px-2 py-1 w-full mb-2"
                        >

                            <option value="non_member">
                                Non Member
                            </option>

                            <option value="member">
                                Member
                            </option>

                        </select>


                        <select
                            name="payment_method"
                            class="border rounded px-2 py-1 w-full mb-2"
                        >

                            <option value="cash">
                                Cash
                            </option>

                            <option value="transfer">
                                Transfer / QRIS
                            </option>

                            <option value="hutang">
                                Hutang
                            </option>

                        </select>



                        <input
                            type="hidden"
                            name="total_discount"
                            id="finalDiscountInput"
                        >



                        <button
                            type="submit"
                            class="w-full bg-green-600 text-white py-3 rounded-lg"
                        >
                            Bayar
                        </button>

                    </form>

                </div>

            </div>



            <!-- ================================= -->
            <!-- PANEL KANAN : GAMBAR -->
            <!-- ================================= -->

            <div class="w-1/2 bg-white rounded-xl shadow p-4 overflow-y-auto">

                <div
                    id="cartImages"
                    class="grid grid-cols-3 gap-4"
                ></div>

            </div>

        </div>

    </div>



<script>


/* ======================================
   LOAD CART
====================================== */

function loadCart()
{

    fetch("{{ route('cart.get') }}")

        .then(response => response.json())

        .then(data => {

            renderCart(data.cart)

        })

}

loadCart()



/* ======================================
   SCAN BARANG
====================================== */

const form  = document.getElementById('scanForm')
const input = document.getElementById('scanCode')

form.addEventListener('submit', function(e){

    e.preventDefault()

    fetch("{{ route('cart.add.code') }}", {

        method : "POST",

        headers : {

            "Content-Type" : "application/json",
            "X-CSRF-TOKEN" : "{{ csrf_token() }}"

        },

        body : JSON.stringify({

            code : input.value

        })

    })

    .then(res => res.json())

    .then(data => {

        if(data.error){

            alert(data.error)
            return

        }

        renderCart(data.cart)

    })

    input.value = ''
    input.focus()

})



/* ======================================
   RENDER CART
====================================== */

function renderCart(cart)
{

    const table  = document.getElementById('cartItems')
    const images = document.getElementById('cartImages')
    const total  = document.getElementById('totalPrice')

    table.innerHTML  = ''
    images.innerHTML = ''

    let totalPrice = 0


    Object.values(cart).forEach(item => {

        let price    = item.sell_price ?? item.price ?? 0
        let discount = item.discount ?? 0


        if(typeof discount === "string" && discount.includes('%')){

            let percent = parseInt(discount.replace('%',''))

            discount = (price * item.qty) * percent / 100

        }


        let subtotal = (price * item.qty) - discount

        totalPrice += subtotal



        table.innerHTML += `

            <tr class="border-b">

                <td class="py-2">

                    ${item.code}

                </td>

                <td>

                    ${item.name}

                </td>

                <td class="text-center">

                    <button
                        class="qty-minus px-2"
                        data-id="${item.id}"
                        data-qty="${item.qty}"
                    >
                        -
                    </button>

                    ${item.qty}

                    <button
                        class="qty-plus px-2"
                        data-id="${item.id}"
                        data-qty="${item.qty}"
                    >
                        +
                    </button>

                </td>

                <td>

                    <input
                        class="discount-input border w-16 text-center"
                        data-id="${item.id}"
                        value="${item.discount ?? 0}"
                    >

                </td>

                <td class="text-right">

                    ${formatRupiah(subtotal)}

                </td>

            </tr>

        `



        images.innerHTML += `

            <div class="border rounded p-2 text-center">

                <div class="text-xs text-gray-500">

                    ${item.code}

                </div>

                <img
                    src="/storage/${item.image}"
                    class="h-28 mx-auto object-contain"
                >

                <div class="text-sm font-semibold mt-2">

                    ${item.name}

                </div>

            </div>

        `

    })



    let totalDiscount = document
        .getElementById('totalDiscount')
        .value ?? 0


    if(typeof totalDiscount === "string" && totalDiscount.includes('%')){

        let percent = parseInt(totalDiscount.replace('%',''))

        totalDiscount = totalPrice * percent / 100

    }


    let finalTotal = totalPrice - totalDiscount


    total.innerText = formatRupiah(finalTotal)


    document
        .getElementById('finalDiscountInput')
        .value = totalDiscount

}



/* ======================================
   QTY BUTTON
====================================== */

document.addEventListener('click', function(e){

    if(e.target.classList.contains('qty-plus')){

        let id  = e.target.dataset.id
        let qty = parseInt(e.target.dataset.qty) + 1

        updateQty(id, qty)

    }

    if(e.target.classList.contains('qty-minus')){

        let id  = e.target.dataset.id
        let qty = parseInt(e.target.dataset.qty) - 1

        if(qty < 1){

            qty = 1

        }

        updateQty(id, qty)

    }

})



function updateQty(id, qty)
{

    fetch("{{ route('cart.update.qty') }}", {

        method : "POST",

        headers : {

            "Content-Type" : "application/json",
            "X-CSRF-TOKEN" : "{{ csrf_token() }}"

        },

        body : JSON.stringify({

            id  : id,
            qty : qty

        })

    })

    .then(res => res.json())

    .then(data => {

        renderCart(data.cart)

    })

}



/* ======================================
   FORMAT RUPIAH
====================================== */

function formatRupiah(angka)
{

    return "Rp " + angka.toLocaleString('id-ID')

}

</script>

</x-app-layout>