<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Libraries\AppLibrary;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Services\PermissionService;
use App\Http\Resources\RoleResource;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class PermissionController extends AdminController implements HasMiddleware
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();
        $this->permissionService = $permissionService;
    }

    Public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(Role $role): \Illuminate\Foundation\Application|\Illuminate\Http\Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $permissions     = Permission::get();
            $rolePermissions = Permission::join(
                "role_has_permissions",
                "role_has_permissions.permission_id",
                "=",
                "permissions.id"
            )->where("role_has_permissions.role_id", $role->id)->get()->pluck('name', 'id');
            $permissions     = AppLibrary::permissionWithAccess($permissions, $rolePermissions);
            $permissions     = AppLibrary::numericToAssociativeArrayBuilder($permissions->toArray());
            return new JsonResponse(['data' => $permissions], 201);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PermissionRequest $request, Role $role): RoleResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RoleResource($this->permissionService->update($request, $role));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
