<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Analytic;
use App\Services\AnalyticService;
use App\Http\Requests\AnalyticRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AnalyticResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AnalyticController extends AdminController implements HasMiddleware
{
    private AnalyticService $analyticService;

    public function __construct(AnalyticService $analyticService)
    {
        parent::__construct();
        $this->analyticService = $analyticService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'store', 'update', 'show', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AnalyticResource::collection($this->analyticService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(AnalyticRequest $request): AnalyticResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AnalyticResource($this->analyticService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(AnalyticRequest $request, Analytic $analytic): AnalyticResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AnalyticResource($this->analyticService->update($request, $analytic));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Analytic $analytic): AnalyticResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AnalyticResource($this->analyticService->show($analytic));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function destroy(Analytic $analytic): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->analyticService->destroy($analytic);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
