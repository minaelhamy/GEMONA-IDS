<?php

namespace App\Services;

use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Libraries\AppLibrary;
use App\Services\MenuService;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

abstract class SocialLoginAbstract
{
    public MenuService $menuService;
    public PermissionService $permissionService;

    public function __construct(MenuService $menuService, PermissionService $permissionService)
    {
        $this->menuService = $menuService;
        $this->permissionService = $permissionService;
    }

    abstract public function getUrl();
    
    abstract public function verifySocialLogin();

    public function Login($user)
    {
        $user = $user;
        Auth::guard('web')->login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        $permission        = PermissionResource::collection($this->permissionService->permission($user->roles[0]));
        $defaultPermission = AppLibrary::defaultPermission($permission);
        $defaultMenu       = (object)AppLibrary::defaultMenu($this->menuService->menu($user->roles[0]), $defaultPermission);

        return new JsonResponse([
            'message'           => trans('all.message.login_success'),
            'token'             => $token,
            'user'              => new UserResource($user),
            'menu'              => MenuResource::collection(collect($this->menuService->menu($user->roles[0]))),
            'permission'        => $permission,
            'defaultPermission' => $defaultPermission,
            'defaultMenu'       => $defaultMenu,
        ], 201);
    }
}
