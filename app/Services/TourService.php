<?php

namespace App\Services;

use App\Models\Tour;
use App\Repositories\TourRepository;
use Illuminate\Http\UploadedFile;

class TourService
{
    public function __construct(
        protected TourRepository $repository
    ) {}

    public function list(?string $search, int $perPage = 12)
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): Tour
    {
        return $this->repository->create($this->buildPayload($data));
    }

    public function update(Tour $tour, array $data): Tour
    {
        return $this->repository->update($tour, $this->buildPayload($data, $tour));
    }

    public function toggleStatus(Tour $tour): void
    {
        $tour->update(['is_active' => ! $tour->is_active]);
    }

    public function delete(Tour $tour): void
    {
        $this->repository->delete($tour);
    }

    /**
     * Build the data payload, converting textarea list fields to JSON arrays.
     */
    private function buildPayload(array $data, ?Tour $existing = null): array
    {
        $itineraryPdf = $data['itinerary_pdf'] ?? null;
        if ($itineraryPdf instanceof UploadedFile) {
            $itineraryPdf = $itineraryPdf->store('tours/itineraries', 'public');
        } elseif (empty($itineraryPdf) && $existing?->itinerary_pdf) {
            $itineraryPdf = $existing->itinerary_pdf;
        }

        return [
            'title' => $data['title'],
            'location' => $data['location'],
            'days' => $data['days'] ?? 1,
            'hotel_rating' => $data['hotel_rating'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'retail_price' => $data['retail_price'] ?? 0,
            'agent_price' => $data['agent_price'] ?? 0,
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
            'itinerary' => $data['itinerary'] ?? null,
            'itinerary_pdf' => $itineraryPdf,
            'highlights' => $this->textareaToArray($data['highlights'] ?? ''),
            'whats_included' => $this->textareaToArray($data['whats_included'] ?? ''),
            'image_urls' => $this->normalizeImageUrls($data['image_urls'] ?? null, $existing?->image_urls ?? []),
            'departure_months' => $this->normalizeDepartureMonths($data['departure_months'] ?? null),
            'max_capacity' => $data['max_capacity'] ?? 20,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : ($existing?->is_active ?? true),
        ];
    }

    /**
     * Store uploaded image files and keep existing values when no new files are provided.
     *
     * @param mixed $value
     * @param array<int, string> $existing
     * @return array<int, string>
     */
    private function normalizeImageUrls(mixed $value, array $existing = []): array
    {
        if ($value instanceof UploadedFile) {
            return [$value->store('tours/images', 'public')];
        }

        if (is_array($value)) {
            $storedPaths = [];

            foreach ($value as $item) {
                if ($item instanceof UploadedFile) {
                    $storedPaths[] = $item->store('tours/images', 'public');
                } elseif (is_string($item) && trim($item) !== '') {
                    $storedPaths[] = trim($item);
                }
            }

            if ($storedPaths !== []) {
                return $storedPaths;
            }
        }

        if (is_string($value) && trim($value) !== '') {
            return $this->textareaToArray($value);
        }

        return $existing;
    }

    /**
     * Split a multi-line textarea string into a trimmed array, removing blanks.
     *
     * @return array<string>
     */
    private function normalizeDepartureMonths(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $date = trim($item['date'] ?? '');
                $slots = isset($item['slots']) ? (int) $item['slots'] : 0;

                if ($date === '' || $slots < 1) {
                    return null;
                }

                return [
                    'date' => $date,
                    'slots' => $slots,
                ];
            }, $value), fn ($item) => $item !== null));
        }

        if (is_string($value) && trim($value) !== '') {
            return array_values(array_filter(array_map(function (string $line) {
                $date = trim($line);
                return $date === '' ? null : ['date' => $date, 'slots' => 1];
            }, explode("\n", $value)), fn ($item) => $item !== null));
        }

        return [];
    }

    private function textareaToArray(string $value): array
    {
        return array_values(
            array_filter(
                array_map('trim', explode("\n", $value)),
                fn (string $line) => $line !== ''
            )
        );
    }

    public function listRequests(?string $search = null)
    {
        return $this->repository->paginateRequests($search);
    }
}
