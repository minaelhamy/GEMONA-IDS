<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\State;
use App\Services\StateService;
use App\Http\Requests\StateRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\StateResource;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\StateSimpleResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class StateController extends AdminController implements HasMiddleware
{

    private StateService $stateService;

    public function __construct(StateService $state)
    {
        parent::__construct();
        $this->stateService = $state;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'store', 'update', 'destroy', 'show']),
        ];
    }

    public function index(PaginateRequest $request)
    {
        try {
            return StateResource::collection($this->stateService->list($request));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function simpleLists(PaginateRequest $request)
    {
        try {
            return StateSimpleResource::collection($this->stateService->list($request));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(StateRequest $request)
    {
        try {
            return new StateResource($this->stateService->store($request));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(StateRequest $request, State $state)
    {
        try {
            return new StateResource($this->stateService->update($request, $state));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(State $state)
    {
        try {
            $this->stateService->destroy($state);
            return response('', 202);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function statesByCountry($countryName)
    {
        try {
            return StateResource::collection($this->stateService->statesByCountry($countryName));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
