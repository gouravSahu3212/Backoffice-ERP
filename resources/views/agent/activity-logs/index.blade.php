@extends('layouts.dashboard')

@section('page-title', 'My Activity')

@section('content')

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Activity</h1>
        <p class="text-sm text-gray-500 mt-1">Track updates on your tour requests and bookings</p>
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
                    placeholder="Search by description…"
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
                <a href="{{ route('agent.activity-logs.index') }}"
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
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Action</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Description</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">By</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="activity-log-tbody">
                @include('agent.activity-logs._table')
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

        const baseUrl = url || '{{ route("agent.activity-logs.index") }}';
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

    attachPaginationLinks();
})();
</script>
@endpush
