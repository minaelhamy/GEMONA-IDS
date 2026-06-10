<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\LicenseService;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\LicenseResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class LicenseController extends AdminController implements HasMiddleware
{
    public LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(): \Illuminate\Http\Response | LicenseResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new LicenseResource($this->licenseService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(LicenseRequest $request): \Illuminate\Http\Response | LicenseResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new LicenseResource($this->licenseService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
