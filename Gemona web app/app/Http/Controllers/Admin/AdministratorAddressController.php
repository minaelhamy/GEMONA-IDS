<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Address;
use App\Services\UserAddressService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\AdministratorAddressRequest;

class AdministratorAddressController extends AdminController implements HasMiddleware
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
            new Middleware('permission:administrators_show', only: ['index']),
            new Middleware('permission:administrators_show', only: ['show']),
            new Middleware('permission:administrators_show', only: ['store']),
            new Middleware('permission:administrators_show', only: ['update']),
            new Middleware('permission:administrators_show', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request, User $administrator): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AddressResource::collection($this->userAddressService->list($request, $administrator));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(AdministratorAddressRequest $request, User $administrator): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->store($request, $administrator));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(AdministratorAddressRequest $request, User $administrator, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->update($request, $administrator, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $administrator, Address $address): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->userAddressService->destroy($administrator, $address);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $administrator, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->show($administrator, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
