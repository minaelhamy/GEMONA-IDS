<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use App\Http\Requests\PaginateRequest;
use App\Services\PromotionProductService;
use App\Http\Requests\PromotionProductRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\PromotionProductResource;
use Illuminate\Routing\Controllers\HasMiddleware;

class PromotionProductController extends AdminController implements HasMiddleware
{

    public PromotionProductService $promotionProductService;

    public function __construct(PromotionProductService $promotionProductService)
    {
        parent::__construct();
        $this->promotionProductService = $promotionProductService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:promotions_show', only: ['index', 'store', 'destroy']),
        ];
    }

    public function index(
        PaginateRequest $request,
        Promotion $promotion
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return PromotionProductResource::collection($this->promotionProductService->list($request, $promotion));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(
        PromotionProductRequest $request,
        Promotion $promotion
    ): \Illuminate\Http\Response | PromotionProductResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new PromotionProductResource($this->promotionProductService->store($request, $promotion));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        Promotion $promotion,
        PromotionProduct $promotionProduct
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->promotionProductService->destroy($promotion, $promotionProduct);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
