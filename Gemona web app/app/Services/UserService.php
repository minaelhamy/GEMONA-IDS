<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class UserService
{
    public array $userFilter = ['name', 'email', 'balance', 'phone'];
    public array $roleFilter = ['role_id'];


    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return User::with('media', 'addresses', 'roles')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->roleFilter)) {
                        $query->whereHas('roles', function ($query) use ($request, $key) {
                            $query->where('id', '=', $request);
                        });
                    }
                    if (in_array($key, $this->userFilter)) {
                        if ($key == 'phone') {
                            $query->whereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$request}%"]);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
