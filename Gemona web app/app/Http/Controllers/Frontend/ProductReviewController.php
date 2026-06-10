<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\ProductReview;
use App\Http\Controllers\Controller;
use App\Services\ProductReviewService;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ProductReviewRequest;
use App\Http\Resources\ProductReviewResource;

class ProductReviewController extends Controller
{
    private ProductReviewService $productReviewService;

    public function __construct(ProductReviewService $productReviewService)
    {
        $this->productReviewService = $productReviewService;
    }

    public function store(ProductReviewRequest $request): \Illuminate\Http\Response | ProductReviewResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductReviewResource($this->productReviewService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        ProductReviewRequest $request,
        ProductReview $productReview
    ): \Illuminate\Http\Response | ProductReviewResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new ProductReviewResource($this->productReviewService->update($request, $productReview));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductReview $productReview): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductReviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductReviewResource($this->productReviewService->show($productReview));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function uploadImage(ChangeImageRequest $request, ProductReview $productReview): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductReviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductReviewResource($this->productReviewService->uploadImage($request, $productReview));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deleteImage(ProductReview $productReview, $index): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductReviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductReviewResource($this->productReviewService->deleteImage($productReview, $index));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}