<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Voucher - {{ $booking->booking_reference }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .print-container { border: none !important; shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-10 text-gray-800 antialiased">

    {{-- Top Action Toolbar --}}
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print px-4">
        <a href="{{ route('admin.hotels.bookings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            &larr; Back to Hotel Bookings
        </a>
        <button 
            onclick="window.print()" 
            class="bg-[#0F172A] hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-md transition-colors inline-flex items-center gap-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print / Save as PDF
        </button>
    </div>

    {{-- Printable Card Container --}}
    <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-2xl shadow-xl p-8 print-container">
        
        {{-- Voucher Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Hotel Booking Voucher</h1>
                <p class="text-sm text-gray-500 mt-1">Official Booking Confirmation Receipt</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-[#0F172A] text-white text-xs font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider mb-1">
                    {{ $booking->status }}
                </span>
                <p class="text-xs text-gray-400">Reference ID</p>
                <p class="text-lg font-bold text-gray-900 font-mono">{{ $booking->booking_reference }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            
            {{-- Guest Info --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Guest Information</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm border border-gray-100">
                    <p><span class="text-gray-500 font-medium">Customer Name:</span> <strong class="text-gray-900">{{ $booking->customer_name }}</strong></p>
                    <p><span class="text-gray-500 font-medium">Email:</span> <span class="text-gray-900">{{ $booking->customer_email }}</span></p>
                    <p><span class="text-gray-500 font-medium">Phone:</span> <span class="text-gray-900">{{ $booking->customer_phone ?? '+971551234567' }}</span></p>
                    <p><span class="text-gray-500 font-medium">Guests:</span> <span class="text-gray-900">{{ $booking->guests }} Pax</span></p>
                </div>
            </div>

            {{-- Hotel Info --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hotel Information</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm border border-gray-100">
                    <p><span class="text-gray-500 font-medium">Hotel:</span> <strong class="text-gray-900">{{ $booking->hotel->name ?? 'N/A' }}</strong></p>
                    <p><span class="text-gray-500 font-medium">Location:</span> <span class="text-gray-900">{{ $booking->hotel->location->name ?? 'UAE' }}</span></p>
                    <p><span class="text-gray-500 font-medium">Room Category:</span> <span class="text-gray-900">{{ $booking->roomSlot->room_type_name ?? 'Room' }}</span></p>
                    <p><span class="text-gray-500 font-medium">Rooms Count:</span> <span class="text-gray-900">{{ $booking->rooms_count }} Room(s)</span></p>
                </div>
            </div>

        </div>

        {{-- Stay Summary Table --}}
        <div class="mb-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Reservation Details</h3>
            <table class="w-full text-left text-sm border border-gray-200 rounded-xl overflow-hidden">
                <thead class="bg-gray-100 text-xs text-gray-600 font-bold uppercase">
                    <tr>
                        <th class="px-4 py-3">Check-in</th>
                        <th class="px-4 py-3">Check-out</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Price / Night</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    <tr>
                        <td class="px-4 py-3.5 font-medium">{{ $booking->check_in?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3.5 font-medium">{{ $booking->check_out?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3.5 font-medium">{{ $nights }} Night(s)</td>
                        <td class="px-4 py-3.5 font-medium">{{ number_format($booking->price_per_night, 2) }} {{ $booking->currency }}</td>
                        <td class="px-4 py-3.5 text-right font-bold text-gray-900">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Special Requests / Payment Info --}}
        <div class="grid grid-cols-2 gap-8 pt-4 border-t border-gray-200 text-sm">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Special Requests</p>
                <p class="text-gray-700 font-medium">{{ $booking->special_requests ?? 'None' }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Payment Status</p>
                @if($booking->status === 'confirmed')
                    <p class="text-base font-bold text-emerald-700">paid</p>
                @else
                    <p class="text-base font-bold text-gray-500">-</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">Booked on {{ $booking->created_at?->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        {{-- Footer Disclaimer --}}
        <div class="mt-10 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
            Thank you for choosing our travel services. For any enquiries or modifications, please contact support.
        </div>

    </div>

    <script>
        // Auto trigger print dialog on page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
