<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\SocialMediaService;
use App\Http\Requests\SocialMediaRequest;
use App\Http\Resources\SocialMediaResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SocialMediaController extends AdminController implements HasMiddleware
{
    private SocialMediaService $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        parent::__construct();
        $this->socialMediaService = $socialMediaService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(): \Illuminate\Http\Response|SocialMediaResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SocialMediaResource($this->socialMediaService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(SocialMediaRequest $request): \Illuminate\Http\Response|SocialMediaResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SocialMediaResource($this->socialMediaService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
