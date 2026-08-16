<?php

namespace App\Http\Controllers\Frontend;


use App\Enums\Status;
use App\Models\Analytic;
use App\Models\ThemeSetting;
use App\Http\Controllers\Controller;

class RootController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $analytics = Analytic::with('analyticSections')->where(['status' => Status::ACTIVE])->get();
        $themeLogo = ThemeSetting::where(['key' => 'theme_logo'])->first() ?: new ThemeSetting();

        return view('master', [
            'analytics' => $analytics,
            'favicon' => $themeLogo->logo,
            'socialImage' => asset('images/social/gemona-ids-home-preview.png') . '?v=20260816',
            'canonicalUrl' => url()->current(),
        ]);
    }
}
