<?php

namespace App\Http\AiAgents\Agents;

use App\Enums\Status;
use App\Http\Requests\AiRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\AiAgent;
use App\Services\AiAbstract;
use Exception;
use OpenAI as OpenAIClient;

class Openai extends AiAbstract
{
    public function __construct()
    {
        parent::__construct();
        $this->aiAgent = AiAgent::with('gatewayOptions')->where(['slug' => 'openai'])->first();
        if (!blank($this->aiAgent)) {
            $this->aiAgentOption = $this->aiAgent->gatewayOptions->pluck('value', 'option');
            if (env('DEMO')) {
                $this->agent = OpenAIClient::client(env('OPENAI_API_KEY'));
            } else {
                $this->agent = OpenAIClient::client($this->aiAgentOption['openai_api_key']);
            }
        }
    }

    public function status(): bool
    {
        $aiAgent = AiAgent::where(['slug' => 'openai', 'status' => Status::ACTIVE])->first();
        return !blank($aiAgent);
    }

    /**
     * @throws Exception
     */
    public function name(AiRequest $aiRequest): array|string|\Illuminate\Contracts\Translation\Translator|null
    {
        try {
            $response = $this->agent->responses()->create([
                'model' => 'gpt-4o',
                'input' => $this->buildProductNamePrompt($aiRequest->name),
            ]);

            if ($response->status === 'completed') {
                return $response->outputText;
            } else {
                return trans('all.message.agent_is_not_active');
            }
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function description(AiRequest $aiRequest): array|string|\Illuminate\Contracts\Translation\Translator|null
    {
        try {
            $response = $this->agent->responses()->create([
                'model' => 'gpt-4o',
                'input' => $this->buildProductDescriptionPrompt($aiRequest->name),
            ]);

            if ($response->status === 'completed') {
                return $response->outputText;
            } else {
                return trans('all.message.agent_is_not_active');
            }
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function message(AiRequest $aiRequest): array|string|\Illuminate\Contracts\Translation\Translator|null
    {
        try {
            $response = $this->agent->responses()->create([
                'model' => 'gpt-4o',
                'input' => $aiRequest->name,
            ]);

            if ($response->status === 'completed') {
                return $response->outputText;
            } else {
                return trans('all.message.agent_is_not_active');
            }
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function tags(AiRequest $aiRequest): array|string|\Illuminate\Contracts\Translation\Translator|null
    {
        try {
            $response = $this->agent->responses()->create([
                'model' => 'gpt-4o',
                'input' => $this->buildProductTagsPrompt($aiRequest->name),
            ]);

            if ($response->status === 'completed') {
                return $response->outputText;
            } else {
                return trans('all.message.agent_is_not_active');
            }
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
