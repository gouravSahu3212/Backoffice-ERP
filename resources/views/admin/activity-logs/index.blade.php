@extends('layouts.dashboard')

@section('page-title', 'Activity Log')

@section('content')

    {{-- Page header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
            <p class="text-sm text-gray-500 mt-1">Track all actions across the platform</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        {{-- Today --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="stat-today">{{ $stats['today'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Activities Today</p>
            </div>
        </div>

        {{-- This Week --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-blue-50 rounded-lg border border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="stat-week">{{ $stats['this_week'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">This Week</p>
            </div>
        </div>

        {{-- Total Logged --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-violet-50 rounded-lg border border-violet-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $logs->total() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total Logged</p>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-100 rounded-md shadow-sm p-4 mb-6">
        <form id="activity-log-filters" class="flex flex-wrap items-end gap-4">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label for="filter-search" class="block text-xs font-medium text-gray-500 mb-1.5">Search</label>
                <input
                    id="filter-search"
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search by description or user…"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
            </div>

            {{-- Action --}}
            <div class="w-44">
                <label for="filter-action" class="block text-xs font-medium text-gray-500 mb-1.5">Action</label>
                <select
                    id="filter-action"
                    name="action"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ ($filters['action'] ?? '') === $action ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div class="w-40">
                <label for="filter-date-from" class="block text-xs font-medium text-gray-500 mb-1.5">From</label>
                <input
                    id="filter-date-from"
                    type="date"
                    name="date_from"
                    value="{{ $filters['date_from'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
            </div>

            {{-- Date To --}}
            <div class="w-40">
                <label for="filter-date-to" class="block text-xs font-medium text-gray-500 mb-1.5">To</label>
                <input
                    id="filter-date-to"
                    type="date"
                    name="date_to"
                    value="{{ $filters['date_to'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
            </div>

            {{-- Filter Button --}}
            <div>
                <button
                    type="submit"
                    id="filter-btn"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>

            {{-- Reset --}}
            <div>
                <a href="{{ route('admin.activity-logs.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-md shadow-sm overflow-hidden">

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">User</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Action</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Description</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Subject</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">IP</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="activity-log-tbody">
                @include('admin.activity-logs._table')
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100" id="activity-log-pagination">
                {{ $logs->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
<script>
(function () {
    const filterForm  = document.getElementById('activity-log-filters');
    const tbody       = document.getElementById('activity-log-tbody');
    const paginationEl= document.getElementById('activity-log-pagination');

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchLogs();
    });

    async function fetchLogs(url) {
        const filterBtn = document.getElementById('filter-btn');
        filterBtn.disabled = true;
        filterBtn.textContent = 'Loading…';

        const baseUrl = url || '{{ route("admin.activity-logs.index") }}';
        const params  = new URLSearchParams(new FormData(filterForm));
        const fetchUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + params.toString();

        try {
            const res = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await res.json();

            if (res.ok) {
                tbody.innerHTML = data.html;
                if (paginationEl) {
                    paginationEl.innerHTML = data.pagination || '';
                }

                // Update stat cards
                if (data.stats) {
                    const todayEl = document.getElementById('stat-today');
                    const weekEl  = document.getElementById('stat-week');
                    if (todayEl) todayEl.textContent = data.stats.today;
                    if (weekEl)  weekEl.textContent = data.stats.this_week;
                }

                // Re-attach pagination link handlers
                attachPaginationLinks();
            }
        } catch (err) {
            console.error('Filter error:', err);
        } finally {
            filterBtn.disabled = false;
            filterBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg> Filter';
        }
    }

    function attachPaginationLinks() {
        if (!paginationEl) return;
        paginationEl.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                fetchLogs(this.href);
            });
        });
    }

    // Attach on initial load
    attachPaginationLinks();
})();
</script>
@endpush
