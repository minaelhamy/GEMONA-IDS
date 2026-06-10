<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Unit;
use App\Services\UnitService;
use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Http\Requests\PaginateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UnitController extends AdminController implements HasMiddleware
{

    public UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        parent::__construct();
        $this->unitService = $unitService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings|products', only: ['index']),
            new Middleware('permission:settings', only: ['show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return UnitResource::collection($this->unitService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Unit $unit): \Illuminate\Http\Response|UnitResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new UnitResource($this->unitService->show($unit));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(UnitRequest $request): \Illuminate\Http\Response|UnitResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new UnitResource($this->unitService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(UnitRequest $request, Unit $unit): \Illuminate\Http\Response|UnitResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new UnitResource($this->unitService->update($request, $unit));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Unit $unit): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->unitService->destroy($unit);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
