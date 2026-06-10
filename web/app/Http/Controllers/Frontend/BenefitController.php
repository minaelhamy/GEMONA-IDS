<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Exception;
use App\Services\BenefitService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\BenefitResource;

class BenefitController extends Controller
{
    private BenefitService $benefitService;

    public function __construct(BenefitService $benefitService)
    {
        $this->benefitService = $benefitService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return BenefitResource::collection($this->benefitService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
