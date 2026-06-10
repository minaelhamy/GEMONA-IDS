<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Damage;
use App\Exports\DamageExport;
use App\Services\DamageService;
use App\Http\Requests\DamageRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\DamageResource;
use App\Http\Resources\DamageDetailsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class DamageController extends AdminController implements HasMiddleware
{
    public DamageService $damageService;

    public function __construct(DamageService $damageService)
    {
        parent::__construct();
        $this->damageService = $damageService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:damages', only: ['index']),
            new Middleware('permission:damages', only: ['export']),
            new Middleware('permission:damages', only: ['downloadAttachment']),
            new Middleware('permission:damage_create', only: ['store']),
            new Middleware('permission:damage_edit', only: ['update']),
            new Middleware('permission:damage_edit', only: ['edit']),
            new Middleware('permission:damage_delete', only: ['destroy']),
            new Middleware('permission:damage_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  DamageResource::collection($this->damageService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(DamageRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DamageResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DamageResource($this->damageService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Damage $damage): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DamageDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DamageDetailsResource($this->damageService->show($damage));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function edit(Damage $damage): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DamageDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DamageDetailsResource($this->damageService->edit($damage));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(DamageRequest $request, Damage $damage): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DamageResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DamageResource($this->damageService->update($request, $damage));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Damage $damage): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->damageService->destroy($damage);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new DamageExport($this->damageService, $request), 'Damages.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function downloadAttachment(Damage $damage)
    {
        try {
            return $this->damageService->downloadAttachment($damage);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
