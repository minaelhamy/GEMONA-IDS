<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\OrderArea;
use App\Services\OrderAreaService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OrderAreaRequest;
use App\Http\Resources\OrderAreaResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OrderAreaController extends AdminController implements HasMiddleware
{
    private OrderAreaService $orderAreaService;

    public function __construct(OrderAreaService $orderAreaService)
    {
        parent::__construct();
        $this->orderAreaService = $orderAreaService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderAreaResource::collection($this->orderAreaService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(OrderAreaRequest $request): \Illuminate\Http\Response | OrderAreaResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderAreaResource($this->orderAreaService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        OrderAreaRequest $request,
        OrderArea $orderArea
    ): \Illuminate\Http\Response | OrderAreaResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new OrderAreaResource($this->orderAreaService->update($request, $orderArea));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        OrderArea $orderArea
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->orderAreaService->destroy($orderArea);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
