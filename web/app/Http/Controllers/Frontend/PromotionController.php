<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Services\PromotionService;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;


class PromotionController extends Controller
{
    private PromotionService $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return PromotionResource::collection($this->promotionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Promotion $promotion): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PromotionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PromotionResource($this->promotionService->show($promotion));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
