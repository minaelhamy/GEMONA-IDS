<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TokenStoreRequest;
use App\Libraries\QueryExceptionLibrary;

class TokenStoreService
{

    /**
     * @throws Exception
     */
    public function webToken(TokenStoreRequest $request): bool
    {
        try {

            $user = User::find(auth()->user()->id);
            $user->web_token = $request->token;
            $user->save();
            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
    /**
     * @throws Exception
     */
    public function deviceToken(TokenStoreRequest $request): bool
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->device_token = $request->token;
            $user->save();
            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
