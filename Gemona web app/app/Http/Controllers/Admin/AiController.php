<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AiChatResource;
use App\Http\Requests\AiRequest;
use App\Models\AiAgent;
use App\Models\AiChatHistory;
use App\Services\AiChatHistoryService;
use Exception;
use App\Services\AiService;
use App\Services\AiUsageService;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AiController extends AdminController implements HasMiddleware
{
    public string $agent;
    public AiService $aiService;
    public AiUsageService $aiUsageService;
    public AiChatHistoryService $aiChatHistoryService;

    public function __construct(AiService $aiService, AiUsageService $aiUsageService, AiChatHistoryService $aiChatHistoryService)
    {
        parent::__construct();
        $this->aiService            = $aiService;
        $this->aiUsageService       = $aiUsageService;
        $this->aiChatHistoryService = $aiChatHistoryService;
        $defaultAiAgent             = Settings::group('site')->get('site_default_ai_agent');
        if ($defaultAiAgent > 0) {
            $agent = AiAgent::find($defaultAiAgent);
            if ($agent) {
                $this->agent = $agent->slug;
            }
        }
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:ai-assistant', only: ['name', 'description', 'chat', 'chatResponse', 'chatHistory']),
        ];
    }

    public function name(AiRequest $aiRequest): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if ($this->aiService->agent($this->agent)->status()) {
                return response(['status' => true, 'data' => $this->aiService->agent($this->agent)->name($aiRequest)], 200);
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function description(AiRequest $aiRequest): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if ($this->aiService->agent($this->agent)->status()) {
                return response(['status' => true, 'data' => $this->aiService->agent($this->agent)->description($aiRequest)], 200);
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function tags(AiRequest $aiRequest): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if ($this->aiService->agent($this->agent)->status()) {
                $tagsResponse = $this->aiService->agent($this->agent)->tags($aiRequest);
                $parsedTags = [];

                if (is_string($tagsResponse)) {
                    $decoded = json_decode($tagsResponse, true);
                    if (is_array($decoded) && !in_array('INVALID_INPUT', $decoded)) {
                        $parsedTags = $decoded;
                    }
                }

                return response(['status' => true, 'data' => $parsedTags], 200);
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chat(AiRequest $aiRequest): \Illuminate\Http\Response|AiChatResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if ($this->aiService->agent($this->agent)->status()) {
                return new AiChatResource($this->aiChatHistoryService->store($aiRequest));
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chatResponse(AiChatHistory $aiChatHistory, AiRequest $aiRequest): \Illuminate\Http\Response|AiChatResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AiChatResource($this->aiChatHistoryService->update($aiChatHistory, $this->aiService->agent($this->agent)->message($aiRequest)));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chatHistory(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AiChatResource::collection($this->aiChatHistoryService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function status(): \Illuminate\Http\Response|bool|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['status' => false, 'data' => $this->aiUsageService->checking()], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
