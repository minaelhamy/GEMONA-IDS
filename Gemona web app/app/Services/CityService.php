<?php

namespace App\Services;



use App\Enums\Status;
use Carbon\Carbon;
use Exception;
use App\Models\City;
use App\Models\State;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CityRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class CityService
{
    public object $city;
    protected array $cityFilter = [
        'name',
        'code',
        'state_id',
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
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return City::with('state')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->cityFilter)) {
                        if ($key == "state_id") {
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
    public function store(CityRequest $request)
    {
        try {
            $this->city = City::create([
                'name'             => $request->name,
                'state_id'         => $request->state,
                'status'           => $request->status,
            ]);
            return $this->city;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CityRequest $request, City $city)
    {
        try {
            DB::transaction(function () use ($request, $city) {
                $city->name             = $request->name;
                $city->state_id         = $request->state;
                $city->status           = $request->status;
                $city->save();

                $this->city = $city;
            });
            return $this->city;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(City $city): void
    {
        try {
            $city->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function citiesByState($state_name)
    {
        $state = State::where('name', '=', $state_name)->first();
        if (!$state) {
            return [];
        }
        try {
            return City::with('state')
                ->where('status', Status::ACTIVE)
                ->where('state_id', $state->id)->orderBy('name', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
