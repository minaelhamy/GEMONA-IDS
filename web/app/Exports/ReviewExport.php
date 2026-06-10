<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Requests\PaginateRequest;
use App\Services\ReviewService;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReviewExport implements FromCollection,WithHeadings
{

    public ReviewService $reviewService;
    public PaginateRequest $request;

    public function __construct(ReviewService $reviewService, $request)
    {
        $this->reviewService = $reviewService;
        $this->request         = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $reviewArr = [];
        $reviewsArr = $this->reviewService->list($this->request);

        foreach ($reviewsArr as $returnOrder) {
            $reviewArr[] = [
                $returnOrder->star,
                $returnOrder->review,
                $returnOrder->product?->name,
                $returnOrder->user?->name,
            ];
        }
        return collect($reviewArr);
    }
    public function headings(): array
    {
        return [
            trans('all.label.rating'),
            trans('all.label.review'),
            trans('all.label.product'),
            trans('all.label.customer'),
        ];
    }
}
