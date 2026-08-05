@extends('layouts.dashboard')

@section('page-title', 'Transfer Booking Enquiries')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transfer Booking Enquiries</h1>
            <p class="text-sm text-gray-500 mt-1">View your submitted transfer booking enquiries and track status updates.</p>
        </div>

        <form method="GET" action="{{ route('agent.transfer-requests.index') }}" class="w-full lg:w-72">
            <label for="requests-search" class="sr-only">Search enquiries</label>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Request ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Transfer Service</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Route</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Passport</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pickup Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Vehicle</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Total Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Request ID --}}
                            <td class="px-4 py-3 text-sm font-semibold text-[#0B1527] whitespace-nowrap">
                                {{ $req->request_reference }}
                            </td>

                            {{-- Service --}}
                            <td class="px-4 py-3 text-sm text-gray-700 max-w-[180px]">
                                <span class="block truncate text-[#0B1527] font-medium" title="{{ $req->title }}">
                                    {{ $req->title }}
                                </span>
                            </td>

                            {{-- Route --}}
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $req->route_label }}
                            </td>

                            {{-- Customer --}}
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-medium">{{ $req->customer_name }}</div>
                                <div class="text-xs text-gray-400">DOB: {{ $req->date_of_birth?->format('Y-m-d') }}</div>
                            </td>

                            {{-- Passport --}}
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap uppercase">
                                {{ $req->passport_number }}
                            </td>

                            {{-- Pickup Date & Time --}}
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                <div>{{ $req->pickup_date ? $req->pickup_date->format('M d, Y') : '—' }}</div>
                                @if($req->pickup_time)
                                    <div class="text-xs text-gray-400">{{ $req->pickup_time }}</div>
                                @endif
                            </td>

                            {{-- Vehicle --}}
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $req->vehicle ?? 'Standard' }}
                            </td>

                            {{-- Price --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ $req->currency }} {{ number_format((float) $req->total_price, 2) }}
                            </td>

                            {{-- Submitted Date --}}
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                {{ $req->created_at->format('Y-m-d') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                @php
                                    $statusClasses = match($req->status) {
                                        'new' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'contacted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'closed' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">
                                No transfer booking enquiries found.
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
@endsection
