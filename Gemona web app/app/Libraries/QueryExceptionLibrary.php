<?php

namespace App\Libraries;


use Exception;
use Illuminate\Database\QueryException;

class QueryExceptionLibrary
{

    /**
     * @param Exception $e
     * @return string
     */
    public static function message(Exception $e): string
    {
        if ($e instanceof QueryException && isset($e->errorInfo[1])) {
            if ($e->errorInfo[1] === 1451) {
                return trans('all.message.resource_already_used');
            } else {
                return env('APP_DEBUG') ? $e->getMessage() : trans('all.message.database_error_message');
            }
        } else {
            return $e->getMessage();
        }
    }
}
