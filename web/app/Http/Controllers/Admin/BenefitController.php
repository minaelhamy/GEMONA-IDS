<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\Benefit;
use App\Services\BenefitService;
use App\Http\Requests\BenefitRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\BenefitResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BenefitController extends AdminController implements HasMiddleware
{
    private BenefitService $benefitService;

    public function __construct(BenefitService $benefitService)
    {
        parent::__construct();
        $this->benefitService = $benefitService;
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
            return BenefitResource::collection($this->benefitService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(BenefitRequest $request): \Illuminate\Http\Response | BenefitResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new BenefitResource($this->benefitService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(
        Benefit $benefit
    ): \Illuminate\Http\Response | BenefitResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new BenefitResource($this->benefitService->show($benefit));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        BenefitRequest $request,
        Benefit $benefit
    ): \Illuminate\Http\Response | BenefitResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new BenefitResource($this->benefitService->update($request, $benefit));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        Benefit $benefit
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->benefitService->destroy($benefit);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
