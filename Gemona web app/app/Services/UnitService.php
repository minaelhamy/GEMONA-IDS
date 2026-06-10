<?php

namespace App\Services;


use App\Libraries\QueryExceptionLibrary;
use Exception;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\UnitRequest;

class UnitService
{
    public object $unit;
    protected array $unitFilter = [
        'name',
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

            return Unit::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->unitFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store(UnitRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->unit = Unit::create($request->validated());
            });
            return $this->unit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(UnitRequest $request, Unit $unit): Unit
    {
        try {
            DB::transaction(function () use ($request, $unit) {
                $unit->update($request->validated());
            });
            return $unit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Unit $unit): void
    {
        try {
            DB::transaction(function () use ($unit) {
                $checkProduct = $unit->products->whereNull('deleted_at');
                if (!blank($checkProduct)) {
                    $unit->delete();
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    $unit->delete();
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Unit $unit): Unit
    {
        try {
            return $unit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
