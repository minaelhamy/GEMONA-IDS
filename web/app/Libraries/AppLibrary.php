<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use InvalidArgumentException;
use App\Enums\CurrencyPosition;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\File;

class AppLibrary
{
    public static function date($date, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT') ?: 'd-m-Y';
        }
        return Carbon::parse($date)->format($pattern);
    }

    public static function time($time, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT') ?: 'h:i A';
        }
        return Carbon::parse($time)->format($pattern);
    }

    public static function datetime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = (env('TIME_FORMAT') ?: 'h:i A') . ', ' . (env('DATE_FORMAT') ?: 'd-m-Y');
        }
        return Carbon::parse($dateTime)->format($pattern);
    }

    public static function increaseDate($dateTime, $days, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT') ?: 'd-m-Y';
        }
        return Carbon::parse($dateTime)->addDays($days)->format($pattern);
    }

    public static function deliveryTime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT');
        }
        $explode = explode('-', $dateTime);
        if (count($explode) == 2) {
            return Carbon::parse(trim($explode[0]))->format($pattern) . ' - ' . Carbon::parse(
                trim($explode[1])
            )->format($pattern);
        }
        return '';
    }

    public static function associativeToNumericArrayBuilder($array): array
    {
        $i = 1;
        $buildArray = [];
        if (count($array)) {
            foreach ($array as $arr) {
                if (isset($arr['children'])) {
                    $children = $arr['children'];
                    unset($arr['children']);

                    $arr['parent'] = 0;
                    $buildArray[$i] = $arr;
                    $parentId = $i;
                    $i++;
                    foreach ($children as $child) {
                        $child['parent'] = $parentId;
                        $buildArray[$i] = $child;
                        $i++;
                    }
                } else {
                    $arr['parent'] = 0;
                    $buildArray[$i] = $arr;
                    $i++;
                }
            }
        }
        return $buildArray;
    }

    public static function numericToAssociativeArrayBuilder($array): array
    {
        $i = 0;
        $parentId = null;
        $parentIncrementId = null;
        $buildArray = [];
        if (count($array)) {
            foreach ($array as $arr) {
                if (!$arr['parent']) {
                    $parentId = $arr['id'];
                    $parentIncrementId = $i;
                    $buildArray[$i] = $arr;
                    $i++;
                }

                if ($arr['parent'] == $parentId) {
                    $buildArray[$parentIncrementId]['children'][] = $arr;
                }
            }
        }
        if ($buildArray) {
            foreach ($buildArray as $key => $build) {
                if ($build['url'] == "#" && !isset($build['children'])) {
                    unset($buildArray[$key]);
                }
            }
        }

        return $buildArray;
    }

    public static function permissionWithAccess(&$permissions, $rolePermissions): object
    {
        if ($permissions) {
            foreach ($permissions as $permission) {
                if (isset($rolePermissions[$permission->id])) {
                    $permission->access = true;
                } else {
                    $permission->access = false;
                }
            }
        }
        return $permissions;
    }

    public static function menu(&$menus, $permissions): array
    {
        if ($menus && $permissions) {
            foreach ($menus as $key => $menu) {
                if (isset($permissions[$menu['url']]) && !$permissions[$menu['url']]['access']) {
                    if ($menu['url'] != '#') {
                        unset($menus[$key]);
                    }
                }
            }
        }
        return $menus;
    }

    public static function pluck($array, $value, $key = null, $type = 'object'): array
    {
        $returnArray = [];
        if ($array) {
            foreach ($array as $item) {
                if ($key != null) {
                    if ($type == 'array') {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item[$value];
                    } else {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item->$value;
                    }
                } elseif ($value == 'obj') {
                    $returnArray[] = $item;
                } elseif ($type == 'array') {
                    $returnArray[] = $item[$value];
                } else {
                    $returnArray[] = $item->$value;
                }
            }
        }
        return $returnArray;
    }

    public static function username($name)
    {
        if ($name) {
            $username = strtolower(str_replace(' ', '', $name)) . rand(1, 999999);
            if (User::where(['username' => $username])->first()) {
                self::username($name);
            }
            return $username;
        }
    }

    public static function name($firstName, $lastName): string
    {
        return $firstName . ' ' . $lastName;
    }

    public static function amountCheck($amount, $attr = 'price'): object
    {
        $response = [
            'status'  => true,
            'message' => ''
        ];

        if (!is_numeric($amount)) {
            $response['status'] = false;
            $response['message'] = "This {$attr} must be integer.";
        }

        if ($amount <= 0) {
            if (!$response['status']) {
                return (object)$response;
            } else {
                $response['status'] = false;
                $response['message'] = "This {$attr} negative amount not allow.";
            }
        }

        $replaceValue = str_replace('.', '', $amount);
        if (strlen($replaceValue) > 12) {
            if (!$response['status']) {
                return (object)$response;
            } else {
                $response['status'] = false;
                $response['message'] = "This {$attr} length can't be greater than 12 digit.";
            }
        }

        return (object)$response;
    }

    public static function currencyAmountFormat($amount): string
    {
        if (env('CURRENCY_POSITION') == CurrencyPosition::LEFT) {
            return env('CURRENCY_SYMBOL') . number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '');
        }
        return number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '') . env('CURRENCY_SYMBOL');
    }

    public static function flatAmountFormat($amount): string
    {
        return number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '');
    }

    public static function convertAmountFormat($amount): float
    {
        return (float)number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', '');
    }

    public static function fcmDataBind($request): void
    {
        $cdn = public_path("firebase-cdn.txt");
        $textContent = public_path("firebase-content.txt");
        $file = public_path("firebase-messaging-sw.js");
        $content = 'let config = {
        apiKey: "' . $request->notification_fcm_api_key . '",
        authDomain: "' . $request->notification_fcm_auth_domain . '",
        projectId: "' . $request->notification_fcm_project_id . '",
        storageBucket: "' . $request->notification_fcm_storage_bucket . '",
        messagingSenderId: "' . $request->notification_fcm_messaging_sender_id . '",
        appId: "' . $request->notification_fcm_app_id . '",
        measurementId: "' . $request->notification_fcm_measurement_id . '",' . "\n" . ' };' . "\n";
        File::put($file, File::get($cdn) . $content . File::get($textContent));
    }

    public static function defaultPermission($permissions)
    {
        $defaultPermission = (object)[];
        if (count($permissions)) {
            foreach ($permissions as $permission) {
                if ($permission->access) {
                    $defaultPermission = $permission;
                    break;
                }
            }
        }
        return $defaultPermission;
    }
    public static function defaultMenu($menus, $defaulPermission): array
    {
        if (!isset($defaulPermission->url)) {
            return [];
        }

        foreach ($menus as $menu) {
            if (isset($menu['url']) && $menu['url'] === $defaulPermission->url) {
                return $menu;
            }
            if (isset($menu['children']) && is_array($menu['children']) && count($menu['children']) > 0) {
                $found = self::defaultMenu($menu['children'], $defaulPermission);
                if (!empty($found)) {
                    return $found;
                }
            }
        }
        return [];
    }

    public static function domain($input): array|string|null
    {
        $input = trim($input, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);

        $link = '';
        if (isset($urlParts['port'])) {
            $link .= ':' . $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $link .= $urlParts['path'];
        }

        return preg_replace('/^www\./', '', ($urlParts['host'] . $link));
    }

    public static function licenseApiResponse($response)
    {
        $header = explode(';', $response->getHeader('Content-Type')[0]);
        $contentType = $header[0];
        if ($contentType == 'application/json') {
            $contents = $response->getBody()->getContents();
            $data = json_decode($contents);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $data;
            }
            return $contents;
        }

        return ['status' => false, 'message' => 'something wrong'];
    }


    public static function deleteDir($dirPath): void
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (!str_ends_with($dirPath, '/')) {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function sku($sku)
    {
        $productVariation = ProductVariation::where(['sku' => $sku])->first();
        $product = Product::where(['sku' => $sku])->first();
        if ($productVariation || $product) {
            self::sku(rand(1000000, 9999999));
        }
        return $sku;
    }

    public static function recursive($elements, $parentId = 0): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = self::recursive($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function tagString($arrays): string
    {
        $string = '';
        $i = 1;
        $count = count($arrays);
        if (count($arrays) > 0) {
            foreach ($arrays as $array) {
                if ($i == $count) {
                    $string .= $array->name;
                } else {
                    $string .= $array->name . ', ';
                }
                $i++;
            }
        }
        return $string;
    }

    public static function taxString($arrays): string
    {
        $string = '';
        $i = 1;
        $count = count($arrays);
        if (count($arrays) > 0) {
            foreach ($arrays as $array) {
                if ($i == $count) {
                    $string .= $array?->tax?->name;
                } else {
                    $string .= $array?->tax?->name . ', ';
                }
                $i++;
            }
        }
        return $string;
    }

    public static function lowerWithReplaceToSpace($string): string
    {
        return strtolower(str_replace($string, '', ' '));
    }

    public static function reportCurrencyAmountFormat($amount): string
    {
        return number_format($amount, env('CURRENCY_DECIMAL_POINT'), '.', ',');
    }

    public static function textShortener($text, $number = 30)
    {
        if ($text && mb_strlen($text) > $number) {
            return mb_substr($text, 0, $number) . "..";
        }
        return $text;
    }

    public static function isBetweenDate($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return false;
        }
        $startDate = Carbon::parse($startDate);
        $endDate   = Carbon::parse($endDate);
        return Carbon::now()->between($startDate, $endDate);
    }

    public static function appVersion(){
        return config('product.version');
    }
}
