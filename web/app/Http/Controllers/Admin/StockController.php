<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Exports\StockExport;
use App\Services\StockService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\StockResource;
use App\Http\Requests\PaginateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class StockController extends AdminController implements HasMiddleware
{
    public StockService $stockService;

    public function __construct(StockService $stockService)
    {
        parent::__construct();
        $this->stockService = $stockService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:stock', only: ['index']),
            new Middleware('permission:stock', only: ['export']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  StockResource::collection($this->stockService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new StockExport($this->stockService, $request), 'Stock.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
