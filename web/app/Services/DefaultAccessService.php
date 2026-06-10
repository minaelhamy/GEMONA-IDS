<?php

namespace App\Services;

use App\Libraries\QueryExceptionLibrary;
use App\Models\DefaultAccess;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DefaultAccessService
{
    /**
     * @throws Exception
     */
    public function show(): array
    {
        try {
            $array         = [];
            $defaultAccess = DefaultAccess::where(['user_id' => Auth::id()])->get();
            if ($defaultAccess) {
                foreach ($defaultAccess as $default) {
                    $array[$default->name] = $default->default_id;
                }
            }
            return $array;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function storeOrUpdate($request = []): array
    {
        try {
            return $this->show();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
