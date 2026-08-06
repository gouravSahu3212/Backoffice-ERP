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
                $activityLogRoute = match ($roleName) {
                    'Super Admin' => 'admin.activity-logs.index',
                    'Agent' => 'agent.activity-logs.index',
                    default => '',
                };
                $unseenCount = app(\App\Services\ActivityLogService::class)->getUnseenCount(auth()->user());
            @endphp

            @foreach ($menuItems as $item)
                @php
                    // $itemRoute = Route::has($item['route']) ? $item['route'] : $fallbackRoute;
                    $itemIcon = match ($item['icon'] ?? '') {
                        'building' => 'building-office',
                        default => $item['icon'] ?? 'document',
                    };
                    $itemBadge = ($item['route'] === $activityLogRoute) ? $unseenCount : 0;
                @endphp

                @if (isset($item['submenu']) && !empty($item['submenu']))
                    @php
                        $submenuActive = collect($item['submenu'])->contains(function ($sub) {
                            $r = Route::has($sub['route']) ? $sub['route'] : null;
                            return $r && (request()->routeIs($r) || request()->routeIs($r . '.*'));
                        });
                        $parentActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                    @endphp

                    <div x-data="{ open: {{ ($submenuActive || $parentActive) ? 'true' : 'false' }} }" class="space-y-0.5">
                        {{-- Parent row: link + collapse toggle --}}
                        <div class="flex items-center rounded-lg transition-colors
                            {{ $parentActive || $submenuActive
                                ? 'bg-gray-900 text-white'
                                : 'text-gray-600 hover:bg-gray-200 hover:text-gray-900' }}">

                            {{-- Clickable link to parent route --}}
                            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                                class="flex items-center gap-3 flex-1 min-w-0 px-3 py-2.5 text-sm font-medium rounded-l-lg transition-colors">
                                <span class="shrink-0 {{ $parentActive || $submenuActive ? 'text-white' : 'text-gray-900' }}">
                                    <x-dynamic-component :component="'heroicon-o-' . $itemIcon" class="w-4 h-4" />
                                </span>
                                <span x-show="sidebarOpen" x-transition class="ml-3 flex-1 truncate">
                                    {{ $item['title'] }}
                                </span>
                                @if($itemBadge > 0)
                                    <span x-show="sidebarOpen" x-transition
                                        class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[11px] font-bold rounded-full
                                        {{ $parentActive || $submenuActive ? 'bg-white text-gray-900' : 'bg-red-500 text-white' }}">
                                        {{ $itemBadge > 99 ? '99+' : $itemBadge }}
                                    </span>
                                @endif
                            </a>

                            {{-- Chevron toggle button --}}
                            <button
                                x-show="sidebarOpen"
                                @click="open = !open"
                                type="button"
                                class="shrink-0 p-2.5 rounded-r-lg transition-colors
                                {{ $parentActive || $submenuActive
                                    ? 'hover:bg-gray-700'
                                    : 'hover:bg-gray-200' }}"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 transition-transform duration-200"
                                    :class="open ? 'rotate-90' : ''"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        {{-- Collapsible sub-items --}}
                        <div
                            x-show="open && sidebarOpen"
                            x-collapse
                            class="pl-4 space-y-0.5"
                        >
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
                    <x-nav-item :route="$item['route']" :badge="$itemBadge">
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
