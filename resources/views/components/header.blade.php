<header class="bg-white border-b border-gray-200">

    <div class="flex justify-between items-center px-6 py-3">

        {{-- Collapse / toggle icon --}}
        <button class="p-1.5 rounded hover:bg-gray-100 text-gray-500 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Right side: role badge + user name --}}
        <div class="flex items-center gap-3">

            <span class="bg-gray-900 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                {{ auth()->user()->getRoleNames()->first() }}
            </span>

            <span class="text-sm text-gray-600 font-medium">
                {{ auth()->user()->name }}
            </span>

        </div>

    </div>

</header>