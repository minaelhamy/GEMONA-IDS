<?php

namespace App\Services;

use Exception;
use App\Models\AiAgent;
use Illuminate\Http\Request;
use App\Models\GatewayOption;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class AiAgentService
{
    public object $gateway;
    protected array $aiAgentFilter = [
        'name',
        'slug',
        'status'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    protected array $withFilter = [
        'require'
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

            return AiAgent::with('gatewayOptions')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->aiAgentFilter)) {
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

                    if (in_array($key, $this->withFilter)) {
                        $query->orWhere(['id' => $request]);
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
                        $this->gateway = AiAgent::find($option->model_id);
                        if (!blank($this->gateway)) {
                            $this->gateway->status = $value;
                            $this->gateway->save();
                        }
                    }
                }
            }
            return $this->gateway;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
