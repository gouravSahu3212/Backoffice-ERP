@extends('layouts.dashboard')

@section('page-title', 'Tour Booking Enquiries')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tour Booking Enquiries</h1>
            <p class="text-sm text-gray-500 mt-1">Enquiries submitted by agents from the tours module.</p>
        </div>

        <form method="GET" action="{{ route('admin.tour-requests.index') }}" class="w-full lg:w-72">
            <label for="requests-search" class="sr-only">Search requests</label>
            <input id="requests-search" name="search" value="{{ $search ?? '' }}" type="search"
                placeholder="Search reference, customer, passport…"
                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Request ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Passport</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Departure</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pax</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Agent</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Status Dropdown --}}
                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <select
                                        class="status-select border border-gray-200 rounded-md pl-2.5 pr-7 py-1.5 text-xs font-medium text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-id="{{ $req->id }}"
                                        data-current="{{ $req->status }}"
                                    >
                                        @foreach(['new', 'confirmed', 'reject'] as $statusOption)
                                            <option value="{{ $statusOption }}" {{ $req->status === $statusOption ? 'selected' : '' }}>
                                                {{ ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <svg class="status-spinner w-4 h-4 text-gray-900 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </td>

                            {{-- Request ID --}}
                            <td class="px-4 py-3 text-sm font-semibold text-[#0B1527] whitespace-nowrap">
                                {{ $req->request_reference }}
                            </td>

                            {{-- Tour --}}
                            <td class="px-4 py-3 text-sm text-gray-700 max-w-[180px]">
                                <span class="block truncate text-[#0B1527] font-medium" title="{{ $req->tour?->title }}">
                                    {{ $req->tour?->title ?? '—' }}
                                </span>
                            </td>

                            {{-- Customer --}}
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-medium">{{ $req->customer_name }}</div>
                                <div class="text-xs text-gray-400">DOB: {{ $req->date_of_birth?->format('Y-m-d') }}</div>
                            </td>

                            {{-- Passport --}}
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $req->passport_number }}
                            </td>

                            {{-- Departure --}}
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                {{ $req->departure_date?->format('M d, Y') ?? '—' }}
                            </td>

                            {{-- Pax --}}
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $req->pax }}
                            </td>

                            {{-- Agent --}}
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                {{ $req->agent?->name ?? '—' }}
                            </td>

                            {{-- Price --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ $req->currency }}
                                {{ number_format((float) $req->total_price, 0) }}
                            </td>

                            {{-- Submitted Date --}}
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                {{ $req->created_at->format('Y-m-d') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center text-sm text-gray-500">
                                No tour requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Toast Notification --}}
    <div id="toast-notification" class="fixed top-5 right-5 z-50 transform transition-all duration-300 translate-y-[-100%] opacity-0 pointer-events-none">
        <div class="bg-[#0B1527] text-white px-5 py-3.5 rounded-xl shadow-2xl flex items-center gap-3 text-sm font-medium border border-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="toast-message">Status updated successfully</span>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    const statusUrl = @json(url('admin/tours/requests'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function showToast(message) {
        const toast = document.getElementById('toast-notification');
        const toastMsg = document.getElementById('toast-message');
        if (!toast || !toastMsg) return;

        toastMsg.textContent = message;
        toast.classList.remove('translate-y-[-100%]', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-[-100%]', 'opacity-0', 'pointer-events-none');
        }, 3000);
    }

    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', async function() {
            const id = this.dataset.id;
            const status = this.value;
            const previous = this.dataset.current;
            const spinner = this.parentElement.querySelector('.status-spinner');

            this.disabled = true;
            if (spinner) spinner.classList.remove('hidden');

            try {
                const res = await fetch(`${statusUrl}/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status }),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.dataset.current = status;

                    // Brief visual feedback
                    this.classList.add('ring-2', 'ring-emerald-400');
                    setTimeout(() => this.classList.remove('ring-2', 'ring-emerald-400'), 1200);

                    // Show toast notification
                    const capitalizedStatus = status.charAt(0).toUpperCase() + status.slice(1);
                    showToast(`Tour enquiry status updated to "${capitalizedStatus}"`);
                } else {
                    this.value = previous;
                    alert('Failed to update status. Please try again.');
                }
            } catch (err) {
                this.value = previous;
                console.error('Status update failed', err);
            } finally {
                this.disabled = false;
                if (spinner) spinner.classList.add('hidden');
            }
        });
    });
})();
</script>
@endpush

@endsection