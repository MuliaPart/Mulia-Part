<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50"> 
    <!-- Primary Navigation Menu --> 
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> 
        <div class="flex justify-between h-16"> 
                <div class="flex"> 
                    <!-- Logo --> 
                    <div class="shrink-0 flex items-center"> 
                        <a href="{{ route('dashboard') }}"> <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /> </a> 
                    </div> 
                    <!-- Navigation Links --> 
                    <div class="text-gray-900 hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"> 
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> 
                            {{ __('Dashboard') }} 
                        </x-nav-link> 
                    </div> 

                    <div class="text-gray-900 hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"> 
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"> 
                            {{ __('Data Base Produk') }} 
                        </x-nav-link> 
                    </div> 

                    <div class="text-gray-900 hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"> 
                        <x-nav-link :href="route('overview')" :active="request()->routeIs('overview')"> 
                            {{ __('Overview') }} 
                        </x-nav-link> 
                    </div> 

                    <div class="relative group sm:ms-10 sm:flex sm:items-center">

                        <!-- Tombol -->
                        <a href="{{ route('sales.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600">
                            Data Penjualan
                            <svg class="ml-1 w-4 h-4 transition-transform duration-200 group-hover:rotate-180"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"/>
                            </svg>
                        </a>

                        <!-- Dropdown -->
                        <div class="absolute left-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl
                                    transform translate-y-1 scale-95
                                    opacity-0 invisible
                                    group-hover:opacity-100 group-hover:visible
                                    group-hover:translate-y-0 group-hover:scale-100
                                    transition-all duration-300 ease-out
                                    z-50">

                            <a href="{{ route('sales.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Data Penjualan
                            </a>

                            <a href="{{ route('sales.riwayat') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Riwayat Produk Terjual
                            </a>
                            <a href="{{ route('sales.history') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Riwayat Kasir
                            </a>
                            <a href="{{ route('sales.riwayatKas') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Riwayat Kas
                            </a>

                        </div>

                    </div> 
                    <div class="text-gray-900 hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"> 
                        <x-nav-link :href="route('katalog')" :active="request()->routeIs('katalog.*')"> 
                            {{ __('Katalog') }} 
                        </x-nav-link> 
                    </div>
                    <div class="text-gray-900 hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"> 
                        <x-nav-link :href="route('kasir')" :active="request()->routeIs('kasir.*')"> 
                            {{ __('Kasir') }} 
                        </x-nav-link> 
                    </div>
                </div> 
                <!-- Settings Dropdown --> 
                <div class="hidden sm:flex sm:items-center sm:ms-6"> 
                    <x-dropdown align="right" width="48"> 
                        <x-slot name="trigger"> 
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"> 
                                <div>{{ Auth::user()->name }}
                                </div> 
                                <div class="ms-1"> <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"> 
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg> 
                                </div> 
                            </button> 
                        </x-slot> 
                        <x-slot name="content"> 
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }} 
                            </x-dropdown-link> 
                            <!-- Authentication --> 
                            <form method="POST" action="{{ route('logout') }}"> 
                                @csrf 
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"> {{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot> 
                    </x-dropdown> 
                </div> 
                <!-- Hamburger --> 
                <div class="-me-2 flex items-center sm:hidden"> 
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"> 
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"> 
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /> 
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> 
                        </svg> 
                    </button> 
                </div> 
            </div> 
        </div> 
        <!-- Responsive Navigation Menu --> 
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden"> 
            <div class="pt-2 pb-3 space-y-1"> 
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> 
                    {{ __('Dashboard') }} 
                </x-responsive-nav-link> 
            </div> 
        <!-- Responsive Settings Options --> 
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"> 
            <div class="px-4"> 
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ Auth::user()->name }}
                </div> 
                <div class="font-medium text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div> 
            </div> 
            <div class="mt-3 space-y-1"> 
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }} 
                </x-responsive-nav-link> 
                <!-- Authentication --> 
                <form method="POST" action="{{ route('logout') }}"> 
                    @csrf 
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }} 
                    </x-responsive-nav-link>
                </form> 
            </div> 
        </div> 
    </div> 
</nav>