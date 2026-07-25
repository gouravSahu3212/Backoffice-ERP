<?php

namespace App\Http\Controllers\Agent;

use App\Models\Tour;
use App\Repositories\TourRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourController
{
    public function __construct(
        protected TourRepository $repository
    ) {}

    public function index()
    {
        $tours = Tour::active()->latest()->get();

        $locations = Tour::active()
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $months = $this->buildMonthOptions();

        return view('agent.tours.index', compact('tours', 'locations', 'months'));
    }

    public function search(Request $request)
    {
        $query = Tour::active()->latest();

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('month')) {
            // departure_months is stored as JSON; match the year-month substring
            $query->where('departure_months', 'like', '%'.$request->month.'%');
        }

        if ($request->filled('travellers') && (int) $request->travellers > 0) {
            $query->where('max_capacity', '>=', (int) $request->travellers);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        $tours = $query->get()->map(fn (Tour $tour) => $this->formatTour($tour));

        return response()->json([
            'success' => true,
            'count'   => $tours->count(),
            'tours'   => $tours,
        ]);
    }

    /**
     * Collect all unique year-month values from departure_months across active tours.
     *
     * @return \Illuminate\Support\Collection<int, array{value: string, label: string}>
     */
    private function buildMonthOptions(): \Illuminate\Support\Collection
    {
        return Tour::active()
            ->pluck('departure_months')
            ->flatten(1)
            ->map(fn ($d) => data_get($d, 'date'))
            ->filter()
            ->map(fn ($date) => substr((string) $date, 0, 7))
            ->unique()
            ->sort()
            ->values()
            ->map(fn ($ym) => [
                'value' => $ym,
                'label' => Carbon::createFromFormat('Y-m', $ym)->format('F Y'),
            ]);
    }

    public function show(Tour $tour)
    {
        $months = collect($tour->departure_months ?? [])
            ->map(fn ($d) => data_get($d, 'date'))
            ->filter()
            ->map(fn ($date) => substr((string) $date, 0, 7))
            ->unique()
            ->sort()
            ->values()
            ->map(fn ($ym) => [
                'value' => $ym,
                'label' => Carbon::createFromFormat('Y-m', $ym)->format('F Y'),
            ]);

        return view('agent.tours.show', compact('tour', 'months'));
    }

    public function availability(Request $request, Tour $tour)
    {
        $departureMonths = collect($tour->departure_months ?? []);

        if ($request->filled('month')) {
            $monthFilter = $request->month;
            $departureMonths = $departureMonths->filter(function ($item) use ($monthFilter) {
                $date = data_get($item, 'date');

                return $date && str_starts_with((string) $date, $monthFilter);
            });
        }

        $travellers = (int) $request->get('travellers', 1);

        $departures = $departureMonths->map(function ($item) use ($tour, $travellers) {
            $dateStr = data_get($item, 'date');
            $slots = (int) data_get($item, 'slots', 0);

            $carbonDate = null;
            try {
                $carbonDate = Carbon::parse($dateStr);
            } catch (\Exception $e) {
                // Ignore parse errors
            }

            $monthName = $carbonDate ? $carbonDate->format('F Y') : $dateStr;
            $isAvailable = $slots >= $travellers && $slots > 0;

            $statusBadge = 'Available';
            $badgeClass = 'bg-[#0B1527] text-white';

            if ($slots <= 0) {
                $statusBadge = 'Sold Out';
                $badgeClass = 'border border-gray-200 text-gray-500';
            } elseif ($slots <= 3 || ! $isAvailable) {
                $statusBadge = 'Limited';
                $badgeClass = 'bg-gray-100 text-gray-700';
            }

            return [
                'date' => $dateStr,
                'month_name' => $monthName,
                'subtitle' => $monthName.' – escorted group',
                'slots' => $slots,
                'seats_text' => $slots > 0 ? ($slots.' seats left') : 'No seats available',
                'status_badge' => $statusBadge,
                'badge_class' => $badgeClass,
                'is_available' => $isAvailable,
                'retail_price' => (float) $tour->retail_price,
                'agent_price' => (float) $tour->agent_price,
                'currency' => $tour->currency,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'count' => $departures->count(),
            'departures' => $departures,
        ]);
    }

    /**
     * Format a tour for the JSON search response.
     *
     * @return array<string, mixed>
     */
    private function formatTour(Tour $tour): array
    {
        $firstImage = collect($tour->image_urls)->first();

        return [
            'id' => $tour->id,
            'title' => $tour->title,
            'location' => $tour->location,
            'days' => $tour->days,
            'hotel_rating' => $tour->hotel_rating,
            'currency' => $tour->currency,
            'agent_price' => (float) $tour->agent_price,
            'summary' => $tour->summary,
            'image_url' => $firstImage ? (str_starts_with($firstImage, 'http') ? $firstImage : Storage::url($firstImage)) : null,
        ];
    }
}
