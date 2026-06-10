<?php

namespace App\Services;

use App\Http\Requests\AiRequest;
use App\Traits\HasAiPrompt;

abstract class AiAbstract
{

    use HasAiPrompt;

    public object $agent;
    public object $aiAgent;

    public object $aiAgentOption;


    public function __construct()
    {
        $this->loadDefaultLanguage();
    }

    abstract public function status(): bool;

    abstract public function name(AiRequest $aiRequest);

    abstract public function description(AiRequest $aiRequest);

    abstract public function message(AiRequest $aiRequest);

    abstract public function tags(AiRequest $aiRequest);
}
