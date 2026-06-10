<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Coupon;
use App\Exports\CouponExport;
use App\Services\CouponService;
use App\Http\Requests\CouponRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CouponResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class CouponController extends AdminController implements HasMiddleware
{

    private CouponService $couponService;

    public function __construct(CouponService $coupon)
    {
        parent::__construct();
        $this->couponService = $coupon;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:coupons', only: ['index']),
            new Middleware('permission:coupons', only: ['export']),
            new Middleware('permission:coupons_create', only: ['store']),
            new Middleware('permission:coupons_edit', only: ['update']),
            new Middleware('permission:coupons_delete', only: ['destroy']),
            new Middleware('permission:coupons_show', only: ['show'])
        ];
    }


    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CouponResource::collection($this->couponService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CouponRequest $request): CouponResource | \Illuminate\Http\Response
    {
        try {
            return new CouponResource($this->couponService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Coupon $coupon): CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CouponResource($this->couponService->show($coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(CouponRequest $request, Coupon $coupon): CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CouponResource($this->couponService->update($request, $coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Coupon $coupon): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->couponService->destroy($coupon);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CouponExport($this->couponService, $request), 'Coupons.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
