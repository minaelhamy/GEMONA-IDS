<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Enums\Ask;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\Organization;
use App\Enums\Activity;
use App\Enums\Status;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use App\Services\MenuService;
use App\Enums\Role as EnumRole;
use Illuminate\Http\JsonResponse;
use App\Services\OtpManagerService;
use App\Services\PermissionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\MenuResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SignupEmailRequest;
use App\Http\Requests\SignupPhoneRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\VerifyPhoneRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignupController extends Controller
{

    private OtpManagerService $otpManagerService;
    public string $token;
    public PermissionService $permissionService;
    public MenuService $menuService;

    public function __construct(OtpManagerService $otpManagerService, PermissionService $permissionService, MenuService $menuService)
    {
        $this->otpManagerService = $otpManagerService;
        $this->permissionService = $permissionService;
        $this->menuService = $menuService;
    }

    public function otpPhone(
        SignupPhoneRequest $request
    ): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->otpManagerService->otpPhone($request);
            return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function otpEmail(
        SignupEmailRequest $request
    ): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->otpManagerService->otpEmail($request);
            return response(['status' => true, 'message' => trans("all.message.check_your_email_for_code")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verifyPhone(
        VerifyPhoneRequest $request
    ): JsonResponse {
        try {
            $this->otpManagerService->verifyPhone($request);
            return new JsonResponse([
                'status' => true,
                'message' => trans('all.message.otp_verify_success')
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verifyEmail(
        VerifyEmailRequest $request
    ): JsonResponse {
        try {
            $this->otpManagerService->verifyEmail($request);
            return new JsonResponse([
                'status' => true,
                'message' => trans('all.message.otp_verify_success')
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function validateRegister(SignupRequest $request)
    {
        return response(['status' => true, 'message' => trans('all.message.the_form_is_valid')]);
    }

    public function organizations()
    {
        return OrganizationResource::collection(
            Organization::where('status', Status::ACTIVE)
                ->orderBy('country')
                ->orderBy('name')
                ->get()
        );
    }

    public function register(SignupRequest $request)
    {

        $user = DB::transaction(function () use ($request) {
            $organization = null;
            if ($request->post('organization_mode') === 'new') {
                $organization = Organization::firstOrCreate(
                    [
                        'name'    => $request->post('organization_name'),
                        'country' => $request->post('country'),
                    ],
                    [
                        'address'   => $request->post('address'),
                        'type'      => 'organization',
                        'status'    => Status::INACTIVE,
                        'is_seeded' => Ask::NO,
                    ]
                );
            } else {
                $organization = Organization::find($request->post('organization_id'));
            }

            $user = User::create([
                'name'              => $request->post('name'),
                'username'          => Str::slug($request->post('name')) . '-' . Str::lower(Str::random(8)),
                'email'             => $request->post('email'),
                'phone'             => $request->post('phone'),
                'country_code'      => $request->post('country_code'),
                'organization_id'   => $organization?->id,
                'signup_country'    => $request->post('country'),
                'signup_address'    => $request->post('address'),
                'email_verified_at' => Carbon::now(),
                'is_guest'          => Ask::NO,
                'status'            => Status::INACTIVE,
                'password'          => Hash::make($request->post('password'))
            ]);

            $user->assignRole(EnumRole::CUSTOMER);

            Address::create([
                'full_name'    => $request->post('name'),
                'email'        => $request->post('email'),
                'country_code' => $request->post('country_code'),
                'phone'        => $request->post('phone'),
                'country'      => $request->post('country'),
                'address'      => $request->post('address'),
                'user_id'      => $user->id,
            ]);

            return $user;
        });

        if ($user) {
            return response(['status' => true, 'message' => trans('all.message.signup_pending_approval')]);
        } else {
            return response(['status' => false, 'message' => trans('all.message.register_not_completed')], 422);
        }
    }

    public function signupLoginVerify(Request $request)
    {
        try {
            $user = null;
            if (isset($request->phone) && !blank($request->phone)) {
                $user = User::where(['phone' => $request->phone, 'country_code' => $request->country_code])->first();
            } else {
                $user = User::where(['email' => $request->email])->first();
            }
            if ($user && (int)$user->status === Status::ACTIVE) {
                Auth::guard('web')->loginUsingId($user->id);
                $this->token = $user->createToken('auth_token')->plainTextToken;
                $permission = PermissionResource::collection($this->permissionService->permission($user->roles[0]));
                $defaultPermission = AppLibrary::defaultPermission($permission);
                return new JsonResponse([
                    'status' => true,
                    'message' => trans('all.message.register_successfully'),
                    'token' => $this->token,
                    'user' => new UserResource($user),
                    'menu' => MenuResource::collection(collect($this->menuService->menu($user->roles[0]))),
                    'permission' => $permission,
                    'defaultPermission' => $defaultPermission,
                ], 201);
            } else if ($user) {
                return response(['status' => false, 'message' => trans('all.message.account_pending_approval')], 422);
            } else {
                return response(['status' => false, 'message' => trans('all.message.register_not_completed')], 422);
            }
        } catch (Exception $exception) {
            return new JsonResponse(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
