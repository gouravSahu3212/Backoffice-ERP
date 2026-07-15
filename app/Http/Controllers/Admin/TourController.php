<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreTourRequest;
use App\Http\Requests\Admin\UpdateTourRequest;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\Request;

class TourController extends AdminController
{
    public function __construct(
        protected TourService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $tours = $this->service->list($search);

        return view('admin.tours.index', compact('tours', 'search'));
    }

    public function store(StoreTourRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour created successfully.');
    }

    public function update(UpdateTourRequest $request, Tour $tour)
    {
        $tour = $this->service->update($tour, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tour updated successfully.',
                'tour' => [
                    'id' => $tour->id,
                    'title' => $tour->title,
                    'location' => $tour->location,
                    'days' => $tour->days,
                    'hotel_rating' => $tour->hotel_rating,
                    'currency' => $tour->currency,
                    'retail_price' => $tour->retail_price,
                    'agent_price' => $tour->agent_price,
                    'is_active' => $tour->is_active,
                ],
            ]);
        }

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour updated successfully.');
    }

    public function toggleStatus(Request $request, Tour $tour)
    {
        $this->service->toggleStatus($tour);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $tour->fresh()->is_active,
            ]);
        }

        return back()->with('success', 'Tour status updated.');
    }

    public function destroy(Tour $tour)
    {
        $this->service->delete($tour);

        return back()->with('success', 'Tour deleted.');
    }

    public function requests(Request $request)
    {
        $search = $request->search;
        $requests = $this->service->listRequests($search);

        return view('admin.tours.requests.index', compact('requests', 'search'));
    }
}
