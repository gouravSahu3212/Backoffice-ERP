<header class="bg-white border-b shadow-sm">

    <div class="flex justify-between items-center px-8 py-4">

        <h2 class="text-xl font-semibold">

            @yield('page-title', 'Dashboard')

        </h2>

        <div class="flex items-center gap-5">

            <span class="text-gray-600">

                {{ auth()->user()->name }}

            </span>

            <span
                class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

                {{ auth()->user()->getRoleNames()->first() }}

            </span>

        </div>

    </div>

</header>