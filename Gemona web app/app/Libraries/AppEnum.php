<?php

namespace App\Libraries;

use App\Enums\Ask;
use App\Enums\Status;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class AppEnum
{
    public static function status($status): int
    {
        $status = strtolower(trim($status));
        if ($status === 'active') {
            return Status::ACTIVE;
        } elseif ($status === 'inactive') {
            return Status::INACTIVE;
        }

        return Status::ACTIVE;
    }

    public static function ask($ask): int
    {
        $ask = strtolower(trim($ask));
        if ($ask === 'yes') {
            return Ask::YES;
        } elseif ($ask === 'no') {
            return Ask::NO;
        }

        return Ask::YES;
    }

    public static function getCategoryId($categoryName): int|null
    {
        if ($categoryName) {
            $category = ProductCategory::where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($categoryName) . '%')->first();
            if ($category) {
                return $category->id;
            }
            return null;
        }
        return null;
    }
}