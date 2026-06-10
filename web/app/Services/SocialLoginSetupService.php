<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\SocialLogin as LoginProvider;
use App\Models\GatewayOption;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class SocialLoginSetupService
{
    public object $provider;
    protected array $socialLoginFilter = [
        'name',
        'slug',
        'status'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

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
            $orderType   = $request->get('order_type') ?? 'asc';

            return LoginProvider::with('gatewayOptions')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->socialLoginFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
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

    /**
     * @param Request $request
     * @return
     * @throws Exception
     */
    public function update($validationRequests): object
    {
        try {
            if (!blank($validationRequests)) {
                foreach ($validationRequests as $key => $value) {
                    $option = GatewayOption::where('option', $key)->first();
                    if (!blank($option)) {
                        $option->value = $value;
                        $option->save();
                    }

                    if (str_contains($key, 'status')) {
                        $this->provider = LoginProvider::find($option->model_id);
                        if (!blank($this->provider)) {
                            $this->provider->status = $value;
                            $this->provider->save();
                        }
                    }
                }
            }
            return $this->provider;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
