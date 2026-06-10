<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\PromotionService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PromotionExport implements FromCollection, WithHeadings
{

    public PromotionService $promotionService;
    public PaginateRequest $request;

    public function __construct(PromotionService $promotionService, $request)
    {
        $this->promotionService = $promotionService;
        $this->request      = $request;
    }

    public function collection()
    {
        $promotionArray  = [];
        $promotionsArray = $this->promotionService->list($this->request);

        foreach ($promotionsArray as $promotion) {
            $promotionArray[] = [
                $promotion->name,
                $promotion->slug,
                trans('promotionType.' . $promotion->type),
                trans('statuse.' . $promotion->status),
            ];
        }
        return collect($promotionArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.slug'),
            trans('all.label.type'),
            trans('all.label.status'),
        ];
    }
}
