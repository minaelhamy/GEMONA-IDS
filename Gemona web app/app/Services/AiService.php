<?php

namespace App\Services;


use App\Http\Requests\AiRequest;

class AiService
{

    public object $ai;

    public function agent(...$args): static
    {

        $agent = '';
        if (count($args) > 0) {
            $agent = ucfirst(array_shift($args));
        }

        if (count($args) == 0) {
            $args = null;
        }

        $className = 'App\\Http\\AiAgents\\Agents\\' . $agent;
        $this->ai  = new $className($args);
        return $this;
    }

    public function status()
    {
        return $this->ai->status();
    }

    public function name(AiRequest $aiRequest)
    {
        return $this->ai->name($aiRequest);
    }

    public function description(AiRequest $aiRequest)
    {
        return $this->ai->description($aiRequest);
    }

    public function tags(AiRequest $aiRequest)
    {
        return $this->ai->tags($aiRequest);
    }

    public function message(AiRequest $request)
    {
        return $this->ai->message($request);
    }
}

