<aside class="w-64 bg-white border-r">

    <div class="p-6">

        <h2 class="text-2xl font-bold">
            Backoffice ERP
        </h2>

        <p class="text-sm text-gray-500">
            Transfers & Hotels
        </p>

    </div>

    <nav class="px-4 space-y-2">

        <x-nav-item route="dashboard">
            Dashboard
        </x-nav-item>

        @role('Super Admin')

            <x-nav-item route="admin.agents.index">
                Agents
            </x-nav-item>

        @endrole

        {{-- <x-nav-item route="transfers">
            Transfers
        </x-nav-item>

        <x-nav-item route="hotels">
            Hotels
        </x-nav-item>

        <x-nav-item route="tours">
            Tours
        </x-nav-item> --}}

        @role('Super Admin')

            {{-- <x-nav-item route="tour-requests">
                Tour Requests
            </x-nav-item> --}}

        @endrole

        {{-- <x-nav-item route="bookings">
            Bookings
        </x-nav-item> --}}

    </nav>

    <div class="p-6">

        <h2 class="text-gray-600">

            {{ auth()->user()->name }}

        </h2>

        <p class="bg-blue-100 text-blue-700 py-1 rounded-full text-xs">

            {{ auth()->user()->getRoleNames()->first() }}

        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="bg-red-500 text-red-600 py-2 rounded hover:bg-black-600">

                < Logout

            </button>

        </form>

    </div>

</aside>