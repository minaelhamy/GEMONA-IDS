<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Resources\SimpleProductResource;
use App\Models\Promotion;
use App\Services\PromotionProductService;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;


class PromotionProductController extends Controller
{
    private PromotionProductService $promotionProductService;

    public function __construct(PromotionProductService $promotionProductService)
    {
        $this->promotionProductService = $promotionProductService;
    }

    public function index(PaginateRequest $request, Promotion $promotion): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleProductResource::collection($this->promotionProductService->productWithPagination($request, $promotion));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
