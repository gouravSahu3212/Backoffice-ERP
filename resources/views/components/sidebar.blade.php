<aside class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen">

    {{-- Logo / Brand --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900 leading-tight">
            Backoffice ERP
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">
            Transfers &amp; Hotels
        </p>
    </div>

    {{-- Navigation --}}
    <div class="px-4 py-4 flex-1">

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-2 mb-2">
            Navigation
        </p>

        <nav class="space-y-0.5">

            @php
                $roleName = auth()->user()->getRoleNames()->first();
                $menuKey = match ($roleName) {
                    'Super Admin' => 'super_admin',
                    'Agent' => 'agent',
                    default => 'agent',
                };
                $menuItems = config("menu.{$menuKey}", []);
                $fallbackRoute = match ($roleName) {
                    'Super Admin' => 'admin.dashboard',
                    'Agent' => 'agent.dashboard',
                    default => 'dashboard',
                };
                echo "menu.{$menuKey}";
            @endphp

            @foreach ($menuItems as $item)
                @php
                    $itemRoute = Route::has($item['route']) ? $item['route'] : $fallbackRoute;
                    $itemIcon = match ($item['icon'] ?? '') {
                        'building' => 'building-office',
                        default => $item['icon'] ?? 'document',
                    };
                @endphp

                @if (isset($item['submenu']) && !empty($item['submenu']))
                    <div class="space-y-1">
                        <x-nav-item :route="$itemRoute">
                            <x-slot:icon>
                                <x-dynamic-component :component="'heroicon-o-' . $itemIcon" class="w-4 h-4" />
                            </x-slot:icon>
                            {{ $item['title'] }}
                        </x-nav-item>

                        <div class="pl-6 space-y-1">
                            @foreach ($item['submenu'] as $subItem)
                                @php
                                    $subRoute = Route::has($subItem['route']) ? $subItem['route'] : $fallbackRoute;
                                    $subIcon = match ($subItem['icon'] ?? '') {
                                        'building' => 'building-office',
                                        default => $subItem['icon'] ?? 'document',
                                    };
                                @endphp
                                <x-nav-item :route="$subRoute">
                                    <x-slot:icon>
                                        <x-dynamic-component :component="'heroicon-o-' . $subIcon" class="w-4 h-4" />
                                    </x-slot:icon>
                                    {{ $subItem['title'] }}
                                </x-nav-item>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-nav-item :route="$itemRoute">
                        <x-slot:icon>
                            <x-dynamic-component :component="'heroicon-o-' . $itemIcon" class="w-4 h-4" />
                        </x-slot:icon>
                        {{ $item['title'] }}
                    </x-nav-item>
                @endif
            @endforeach

        </nav>

    </div>

    {{-- User info + Logout --}}
    <div class="px-6 py-5 border-t border-gray-100">

        <p class="text-sm font-semibold text-gray-800">
            {{ auth()->user()->name }}
        </p>

        <p class="text-xs text-gray-400 mt-0.5">
            {{ auth()->user()->getRoleNames()->first() }}
        </p>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>

    </div>

</aside>
