<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Exports\ReviewExport;
use App\Models\ProductReview;
use App\Services\ReviewService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewDetailsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ReviewController extends AdminController implements HasMiddleware
{
    public ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        parent::__construct();
        $this->reviewService = $reviewService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:reviews', only: ['index']),
            new Middleware('permission:reviews', only: ['export']),
            new Middleware('permission:reviews', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  ReviewResource::collection($this->reviewService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductReview $productReview): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReviewDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReviewDetailsResource($this->reviewService->show($productReview));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReviewExport($this->reviewService, $request), 'Reviews.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

}
