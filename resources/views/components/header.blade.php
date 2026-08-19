<header class="bg-white border-b border-gray-200 sticky top-0 z-10">

    <div class="flex justify-between items-center px-6 py-3">

        {{-- Collapse / toggle icon --}}
        <button
            @click="toggleSidebar()"
            type="button"
            class="p-1.5 rounded hover:bg-gray-100 text-gray-500 transition"
        >
            <svg
                class="w-5 h-5 transition-transform duration-300"
                :class="{ 'rotate-180': !sidebarOpen }"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Right side: activity bell + role badge + user name --}}
        <div class="flex items-center gap-3">

            {{-- Activity Log bell --}}
            @php
                $activityRoute = auth()->user()->hasRole('Super Admin')
                    ? 'admin.activity-logs.index'
                    : 'agent.activity-logs.index';
                $unseenCount = app(\App\Services\ActivityLogService::class)->getUnseenCount(auth()->user());
            @endphp
            <a href="{{ route($activityRoute) }}"
               title="Activity Log"
               class="p-1.5 rounded hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                </svg>
                @if($unseenCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full leading-none">
                        {{ $unseenCount > 99 ? '99+' : $unseenCount }}
                    </span>
                @endif
            </a>

            <span class="bg-gray-900 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                {{ auth()->user()->getRoleNames()->first() }}
            </span>

            <span class="text-sm text-gray-600 font-medium">
                {{ auth()->user()->name }}
            </span>

        </div>

    </div>

</header>