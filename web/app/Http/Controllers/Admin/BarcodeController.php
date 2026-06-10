<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\BarcodeService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\BarcodeResource;

class BarcodeController extends AdminController
{

    public BarcodeService $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        parent::__construct();
        $this->barcodeService = $barcodeService;
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return BarcodeResource::collection($this->barcodeService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
