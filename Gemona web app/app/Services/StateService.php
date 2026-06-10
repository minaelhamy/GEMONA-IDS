<?php

namespace App\Services;



use App\Enums\Status;
use Carbon\Carbon;
use Exception;
use App\Models\State;
use App\Models\Country;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StateRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class StateService
{
    public object $state;
    protected array $stateFilter = [
        'name',
        'country_id',
        'status',
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
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_type') ?? 'desc';

            return State::with('country')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->stateFilter)) {
                        if ($key == "country_id") {
                            $query->where($key, $request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
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
     * @throws Exception
     */
    public function store(StateRequest $request)
    {
        try {
            $this->state = State::create([
                'name' => $request->name,
                'country_id' => $request->country,
                'status' => $request->status,
            ]);
            return $this->state;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(StateRequest $request, State $state)
    {
        try {
            DB::transaction(function () use ($request, $state) {
                $state->name = $request->name;
                $state->country_id = $request->country;
                $state->status = $request->status;
                $state->save();

                $this->state = $state;
            });
            return $this->state;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(State $state): void
    {
        try {
            $state->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function statesByCountry($country_name)
    {
        $country  = Country::where('name', '=', $country_name)->first();
        if (!$country) {
            return [];
        }
        try {
            return State::with('country')
                ->where('status', Status::ACTIVE)
                ->where('country_id', $country->id)->orderBy('name', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
