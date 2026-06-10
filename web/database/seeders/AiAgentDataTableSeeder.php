<?php

namespace Database\Seeders;


use App\Enums\Activity;
use App\Models\AiAgent;
use App\Models\GatewayOption;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;

class AiAgentDataTableSeeder extends Seeder
{

    public array $gateways = [
        [
            "slug"    => "openai",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'openai_api_key',
                    "value"  => 'sksfsf-proj-trHSbeN5sGQXEnDXCoe3FIF-KDbX5g7ztS4-Zs-Tin2cG9JoYGgb775iwxw9NX7XRE7YM_DSHS680WSYRmsfsfbgc3wVHFxFqNxi-jRPh4JqVjP5XQW8rHTWqBN8A',
                ],
                [
                    "option" => 'openai_status',
                    "value"  => Activity::ENABLE
                ],
            ]
        ]
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->gateways as $gateway) {
                $aiAgent = AiAgent::where(['slug' => $gateway['slug']])->first();
                if ($aiAgent) {
                    $aiAgent->status = $gateway['status'];
                    $aiAgent->save();
                }
                $this->agentOption($gateway['options']);
            }
        }
    }

    public function agentOption($options): void
    {
        if (!blank($options)) {
            foreach ($options as $option) {
                $gatewayOption = GatewayOption::where(['option' => $option['option']])->first();
                if ($gatewayOption) {
                    $gatewayOption->value = $option['value'];
                    $gatewayOption->save();
                }
            }
        }
    }
}
