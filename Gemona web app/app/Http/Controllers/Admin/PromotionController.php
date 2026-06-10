<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Promotion;
use App\Exports\PromotionExport;
use App\Services\PromotionService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PromotionRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Resources\PromotionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PromotionController extends AdminController implements HasMiddleware
{

    private PromotionService $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        parent::__construct();
        $this->promotionService = $promotionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:promotions', only: ['index']),
            new Middleware('permission:promotions', only: ['export']),
            new Middleware('permission:promotions', only: ['changeImage']),
            new Middleware('permission:promotions_create', only: ['store']),
            new Middleware('permission:promotions_edit', only: ['update']),
            new Middleware('permission:promotions_delete', only: ['destroy']),
            new Middleware('permission:promotions_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return PromotionResource::collection($this->promotionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(PromotionRequest $request): \Illuminate\Http\Response | PromotionResource
    {
        try {
            return new PromotionResource($this->promotionService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Promotion $promotion): \Illuminate\Http\Response | PromotionResource
    {
        try {
            return new PromotionResource($this->promotionService->show($promotion));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PromotionRequest $request, Promotion $promotion): \Illuminate\Http\Response | PromotionResource
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            return new PromotionResource($this->promotionService->update($request, $promotion));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Promotion $promotion): \Illuminate\Http\Response
    {
        try {
            $this->promotionService->destroy($promotion);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new PromotionExport($this->promotionService, $request), 'Promotion.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, Promotion $promotion): \Illuminate\Http\Response | PromotionResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            return new PromotionResource($this->promotionService->changeImage($request, $promotion));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
