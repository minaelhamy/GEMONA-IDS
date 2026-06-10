<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AiAgentResource;
use App\Services\AiAgentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

use Illuminate\Routing\Controllers\HasMiddleware;

class AiAgentController extends AdminController implements HasMiddleware
{
    private AiAgentService $aiAgentService;

    public function __construct(AiAgentService $aiAgentService)
    {
        parent::__construct();
        $this->aiAgentService = $aiAgentService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AiAgentResource::collection($this->aiAgentService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(Request $request): AiAgentResource|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $className          = 'App\\Http\\AiAgents\\Requests\\' . ucfirst($request->ai_agent_type);
        $gateway            = new $className;
        $validationRequests = $request->validate($gateway->rules());
        try {
            return new AiAgentResource($this->aiAgentService->update($validationRequests));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
