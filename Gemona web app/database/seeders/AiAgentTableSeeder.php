<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\Activity;
use App\Enums\InputType;
use App\Models\AiAgent;
use App\Models\GatewayOption;
use Illuminate\Database\Seeder;

class AiAgentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $gateways = [
        [
            "name" => "OpenAI",
            "slug" => "openai",
            "misc" => null,
            "status" => Activity::DISABLE,
            "options" => [
                [
                    "option" => 'openai_api_key',
                    "type" => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option" => 'openai_status',
                    "type" => InputType::SELECT,
                    "value" => Activity::DISABLE,
                    "activities" => [
                        Activity::ENABLE => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ],
    ];

    public function run(): void
    {
        foreach ($this->gateways as $gateway) {
            $ai = AiAgent::create([
                'name'   => $gateway['name'],
                'slug'   => $gateway['slug'],
                'misc'   => json_encode($gateway['misc']),
                'status' => Status::INACTIVE
            ]);
            $this->gatewayOption($ai->id, $gateway['options']);
        }
    }

    public function gatewayOption($id, $options): void
    {
        foreach ($options as $option) {
            GatewayOption::create([
                'model_id'   => $id,
                'model_type' => 'App\Models\AiAgent',
                'option'     => $option['option'],
                'value'      => $option['value'] ?? "",
                'type'       => $option['type'],
                'activities' => json_encode($option['activities'])
            ]);
        }
    }
}
