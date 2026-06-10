<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Address;
use App\Services\UserAddressService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AddressResource;
use App\Http\Requests\EmployeeAddressRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class EmployeeAddressController extends AdminController implements HasMiddleware
{

    private UserAddressService $userAddressService;

    public function __construct(UserAddressService $userAddressService)
    {
        parent::__construct();
        $this->userAddressService = $userAddressService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:employees_show', only: ['index', 'store', 'update', 'destroy', 'show']),
        ];
    }

    public function index(PaginateRequest $request, User $employee): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AddressResource::collection($this->userAddressService->list($request, $employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(EmployeeAddressRequest $request, User $employee): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->store($request, $employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(EmployeeAddressRequest $request, User $employee, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->update($request, $employee, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $employee, Address $address): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->userAddressService->destroy($employee, $address);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $employee, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->show($employee, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}