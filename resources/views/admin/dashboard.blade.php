@extends('layouts.dashboard')

@section('page-title', 'Dashboard')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Agents --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['agents'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Agents</p>
            </div>
        </div>

        {{-- Total Bookings --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total Bookings</p>
            </div>
        </div>

        {{-- Hotels --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['hotels'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Hotels</p>
            </div>
        </div>

        {{-- Transfer Rates --}}
        <div class="bg-white border border-gray-100 rounded-md p-5 flex items-center gap-4 shadow-sm">
            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['transfer_rates'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Transfer Rates</p>
            </div>
        </div>

    </div>

    {{-- Feature Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Transfers --}}
        <a class="bg-white border border-gray-100 rounded-md p-6 shadow-sm hover:shadow-md transition" href="{{ route('admin.transfers.index') }}">
            <div class="flex items-center gap-3 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Transfers</h3>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed">
                Manage city-to-city and airport transfer rates. Configure pricing,
                vehicles, and zones.
            </p>
        </a>

        {{-- Hotel Booking --}}
        <a class="bg-white border border-gray-100 rounded-md p-6 shadow-sm hover:shadow-md transition cursor-not-allowed" href="#">
            <div class="flex items-center gap-3 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Hotel Booking</h3>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed">
                Manage hotels, room inventory, and slots. Track bookings and
                payments.
            </p>
        </a>

    </div>

    {{-- Recent Activity --}}
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                View all →
            </a>
        </div>

        <div class="bg-white border border-gray-100 rounded-md shadow-sm overflow-hidden">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-4 px-6 py-4 {{ !$loop->last ? 'border-b border-gray-50' : '' }} hover:bg-gray-50/60 transition-colors">

                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        {{ $activity->user ? strtoupper(substr($activity->user->name, 0, 2)) : 'SY' }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">{{ $activity->user->name ?? 'System' }}</span>
                            <span class="text-gray-500">{{ $activity->description }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            @php
                                $actionColors = [
                                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'updated' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'deleted' => 'bg-red-50 text-red-700 border-red-200',
                                    'toggled_status' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'logged_in' => 'bg-violet-50 text-violet-700 border-violet-200',
                                    'logged_out' => 'bg-gray-50 text-gray-600 border-gray-200',
                                ];
                                $colorClass = $actionColors[$activity->action] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded border {{ $colorClass }}">
                                {{ str_replace('_', ' ', $activity->action) }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400 text-sm">
                    No recent activity yet.
                </div>
            @endforelse
        </div>
    </div>

@endsection
