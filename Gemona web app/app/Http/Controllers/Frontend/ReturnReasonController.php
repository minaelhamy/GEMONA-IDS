<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Resources\ReturnReasonResource;
use Exception;
use App\Services\ReturnReasonService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;

class ReturnReasonController extends Controller
{
    private ReturnReasonService $returnReasonService;

    public function __construct(ReturnReasonService $returnReasonService)
    {
        $this->returnReasonService = $returnReasonService;
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return ReturnReasonResource::collection($this->returnReasonService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
