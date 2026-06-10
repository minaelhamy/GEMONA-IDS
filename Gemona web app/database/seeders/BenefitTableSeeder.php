<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Benefit;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;


class BenefitTableSeeder extends Seeder
{

    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $benefits = [
                [
                    'title'       => 'Professional Service',
                    'description' => 'Efficient customer support from passionate team',
                    'status'      => Status::ACTIVE,
                    'sort'        => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => 'Secure Payment',
                    'description' => 'Different secure payment methods',
                    'status'      => Status::ACTIVE,
                    'sort'        => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => 'Fast Delivery',
                    'description' => 'Fast and convenient door to door delivery',
                    'status'      => Status::ACTIVE,
                    'sort'        => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => 'Quality & Savings',
                    'description' => 'Comprehensive quality control and affordable prices',
                    'status'      => Status::ACTIVE,
                    'sort'        => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
            ];
            foreach ($benefits as $benefit) {
                $benefitObject = Benefit::create($benefit);
                if (file_exists(public_path('/images/seeder/benefit/' . strtolower(str_replace(' ', '_', $benefit['title'])) . '.png'))) {
                    $benefitObject->addMedia(public_path('/images/seeder/benefit/' . strtolower(str_replace(' ', '_', $benefit['title'])) . '.png'))->preservingOriginal()->toMediaCollection('benefit');
                }
            }
        }
    }
}