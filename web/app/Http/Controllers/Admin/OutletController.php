<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Outlet;
use App\Services\OutletService;
use App\Http\Requests\OutletRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\OutletResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class OutletController extends AdminController implements HasMiddleware
{

    public OutletService $outletService;

    public function __construct(OutletService $outlet)
    {
        parent::__construct();
        $this->outletService = $outlet;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OutletResource::collection($this->outletService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Outlet $outlet): OutletResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OutletResource($this->outletService->show($outlet));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OutletRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OutletResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OutletResource($this->outletService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(OutletRequest $request, Outlet $outlet): OutletResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OutletResource($this->outletService->update($request, $outlet));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Outlet $outlet): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->outletService->destroy($outlet);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
