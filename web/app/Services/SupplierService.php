<?php

namespace App\Services;

use Exception;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\SupplierRequest;
use App\Libraries\QueryExceptionLibrary;

class SupplierService
{
    public object $user;
    public object $supplier;
    public array $supplierFilter = ['name', 'email', 'phone'];


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

            return Supplier::with('media')->where(
                function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->supplierFilter)) {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            )->orderBy($orderColumn, $orderType)->$method(
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
    public function store(SupplierRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->supplier = Supplier::create($request->validated());
                if ($request->image) {
                    $this->supplier->addMediaFromRequest('image')->toMediaCollection('supplier');
                }
            });
            return $this->supplier;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(SupplierRequest $request, Supplier $supplier): object
    {
        try {
            DB::transaction(function () use ($supplier, $request) {
                $this->supplier               = $supplier;
                $this->supplier->company      = $request->company;
                $this->supplier->name         = $request->name;
                $this->supplier->email        = $request->email;
                $this->supplier->phone        = $request->phone;
                $this->supplier->country_code = $request->country_code;
                $this->supplier->address      = $request->address;
                $this->supplier->country      = $request->country;
                $this->supplier->state        = $request->state;
                $this->supplier->city         = $request->city;
                $this->supplier->zip_code     = $request->zip_code;
                $this->supplier->save();

                if ($request->image) {
                    $this->supplier->clearMediaCollection('supplier');
                    $this->supplier->addMediaFromRequest('image')->toMediaCollection('supplier');
                }
            });
            return $this->supplier;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Supplier $supplier): Supplier
    {
        try {
            return $supplier;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */

    public function destroy(Supplier $supplier): void
    {
        try {
            DB::transaction(function () use ($supplier) {
                $supplier->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
