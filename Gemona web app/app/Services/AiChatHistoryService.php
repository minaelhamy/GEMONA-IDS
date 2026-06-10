<?php

namespace App\Services;


use App\Http\Requests\AiRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\AiChatHistory;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiChatHistoryService
{

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return AiChatHistory::where(['user_id' => Auth::user()->id, 'ai_agent_id' => Settings::group('site')->get('site_default_ai_agent')])->orderBy('id', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(AiRequest $aiRequest)
    {
        try {
            return AiChatHistory::create([
                'user_id'       => Auth::user()->id,
                'ai_agent_id'   => Settings::group('site')->get('site_default_ai_agent'),
                'message'       => $aiRequest->name
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(AiChatHistory $aiChatHistory, $response): AiChatHistory
    {
        try {
            if(!$aiChatHistory->response) {
                $aiChatHistory->response = $response;
                $aiChatHistory->save();
            }
            return $aiChatHistory;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
