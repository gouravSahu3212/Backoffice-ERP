@extends('layouts.dashboard')

@section('page-title', 'Dashboard')

@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    {{-- Agents --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
        <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['agents'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Agents</p>
        </div>
    </div>

    {{-- Total Bookings --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
        <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total Bookings</p>
        </div>
    </div>

    {{-- Hotels --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
        <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['hotels'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Hotels</p>
        </div>
    </div>

    {{-- Transfer Rates --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center gap-4 shadow-sm">
        <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
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
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Transfers</h3>
        </div>
        <p class="text-sm text-blue-500 leading-relaxed">
            Manage city-to-city and airport transfer rates. Configure pricing,
            vehicles, and zones.
        </p>
    </div>

    {{-- Hotel Booking --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Hotel Booking</h3>
        </div>
        <p class="text-sm text-blue-500 leading-relaxed">
            Manage hotels, room inventory, and slots. Track bookings and
            payments.
        </p>
    </div>

</div>

@endsection