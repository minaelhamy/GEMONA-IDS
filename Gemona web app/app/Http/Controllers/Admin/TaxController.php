<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Tax;
use App\Services\TaxService;
use App\Http\Requests\TaxRequest;
use App\Http\Resources\TaxResource;
use App\Http\Requests\PaginateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TaxController extends AdminController implements HasMiddleware
{

    public TaxService $taxService;

    public function __construct(TaxService $taxService)
    {
        parent::__construct();
        $this->taxService = $taxService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings|products|purchase|damages|return-orders', only: ['index']),
            new Middleware('permission:settings', only: ['show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return TaxResource::collection($this->taxService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(TaxRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|TaxResource
    {
        try {
            return new TaxResource($this->taxService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(TaxRequest $request, Tax $tax): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|TaxResource
    {
        try {
            return new TaxResource($this->taxService->update($request, $tax));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Tax $tax): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->taxService->destroy($tax);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Tax $tax): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|TaxResource
    {
        try {
            return new TaxResource($this->taxService->show($tax));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
