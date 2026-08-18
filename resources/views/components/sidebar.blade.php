<aside
    class="bg-gray-100 border-r border-gray-200 flex flex-col h-screen sticky top-0 transition-all duration-300"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>

    {{-- Logo / Brand --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <template x-if="sidebarOpen">
            <div>
                <h2 class="text-lg font-bold">{{ config('app.name') }}</h2>
                <p class="text-xs text-gray-400">Transfers & Hotels</p>
            </div>
        </template>

        <template x-if="!sidebarOpen">
            <div class="flex justify-center">
                <div
                    class="w-10 h-10 rounded-lg bg-gray-900 text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr(config('app.name'), 0, 2)) }}
                </div>
            </div>
        </template>
    </div>

    {{-- Navigation --}}
    <div class="px-4 py-4 flex-1 overflow-y-auto">

        <p
            x-show="sidebarOpen"
            x-transition
            class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-2 mb-2">
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
            @endphp

            @foreach ($menuItems as $item)
                @php
                    $itemIcon = match ($item['icon'] ?? '') {
                        'building' => 'building-office',
                        default => $item['icon'] ?? 'document',
                    };
                @endphp

                @if (isset($item['submenu']) && !empty($item['submenu']))
                    @php
                        // Build full submenu: parent's own route first, then children
                        $fullSubmenu = array_merge(
                            [['title' => $item['title'], 'route' => $item['route'], 'icon' => $item['icon'] ?? '']],
                            $item['submenu']
                        );

                        $anyChildActive = collect($fullSubmenu)->contains(function ($sub) {
                            $r = Route::has($sub['route']) ? $sub['route'] : null;
                            return $r && (request()->routeIs($r) || request()->routeIs($r . '.*'));
                        });
                    @endphp

                    <div x-data="{ open: {{ $anyChildActive ? 'true' : 'false' }} }" class="space-y-0.5">
                        {{-- Parent row: toggle-only, never highlighted --}}
                        <button
                            @click="open = !open"
                            type="button"
                            class="flex items-center w-full rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                                {{ $anyChildActive
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-600 hover:bg-gray-200 hover:text-gray-900' }}"
                        >
                            <span class="shrink-0 {{ $anyChildActive ? 'text-white' : 'text-gray-900' }}">
                                <x-dynamic-component :component="'heroicon-o-' . $itemIcon" class="w-4 h-4" />
                            </span>
                            <span x-show="sidebarOpen" x-transition class="ml-3 flex-1 text-left truncate">
                                {{ $item['title'] }}
                            </span>
                            <svg
                                x-show="sidebarOpen"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 shrink-0 transition-transform duration-200"
                                :class="open ? 'rotate-90' : ''"
                                fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Collapsible sub-items --}}
                        <div
                            x-show="open && sidebarOpen"
                            x-collapse
                            class="pl-4 space-y-0.5"
                        >
                            @foreach ($fullSubmenu as $subItem)
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
                    <x-nav-item :route="$item['route']">
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

        <p x-show="sidebarOpen" class="text-sm font-semibold text-gray-800 transition group">
            {{ auth()->user()->name }}
        </p>

        <p x-show="sidebarOpen" class="text-xs text-gray-400 mt-0.5">
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
                <span x-show="sidebarOpen" x-transition class="ml-3 transition group">
                    Logout
                </span>
            </button>
        </form>

    </div>

</aside>
