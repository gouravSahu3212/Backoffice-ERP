@extends('layouts.dashboard')

@section('page-title', 'Tour Booking Enquiries')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tour Booking Enquiries</h1>
            <p class="text-sm text-gray-500 mt-1">Track enquiries you have submitted for escorted tours.</p>
        </div>

        <form method="GET" action="{{ route('agent.tour-requests.index') }}" class="w-full lg:w-72">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Request ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Passport</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Departure</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pax</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Status Badge --}}
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusColors = [
                                        'new' => 'bg-blue-50 text-blue-700',
                                        'confirmed' => 'bg-emerald-50 text-emerald-700',
                                        'reject' => 'bg-gray-100 text-gray-500',
                                        'default' => 'bg-gray-50 text-gray-600 border-gray-200',
                                    ];
                                    $colorClass = $statusColors[$req->status] ?? 'bg-gray-100 text-gray-500';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>

                            {{-- Request ID --}}
                            <td class="px-4 py-3 text-sm font-semibold text-[#0B1527] whitespace-nowrap">
                                {{ $req->request_reference }}
                            </td>

                            {{-- Tour --}}
                            <td class="px-4 py-3 text-sm text-gray-700 max-w-[200px]">
                                <a href="{{ route('agent.tours.show', $req->tour) }}"
                                    class="block truncate text-[#0B1527] font-medium hover:underline"
                                    title="{{ $req->tour?->title }}">
                                    {{ $req->tour?->title ?? '—' }}
                                </a>
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
                            <td colspan="9" class="px-4 py-16 text-center text-sm text-gray-500">
                                No enquiries submitted yet. Go to
                                <a href="{{ route('agent.tours.index') }}" class="text-[#0B1527] font-medium hover:underline">Tours</a>
                                and click <strong>Enquire</strong> on an available departure.
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
