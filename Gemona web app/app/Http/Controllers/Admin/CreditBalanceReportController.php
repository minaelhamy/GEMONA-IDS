<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ThemeSetting;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CompanyService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Exports\CreditBalanceReportExport;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CreditBalanceReportController extends AdminController implements HasMiddleware
{

    private UserService $userService;
    private CompanyService $companyService;

    public function __construct(UserService $userService, CompanyService $companyService)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->companyService = $companyService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:credit-balance-report', only: ['index', 'export', 'exportPdf']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return UserResource::collection($this->userService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CreditBalanceReportExport($this->userService, $request), 'Credit-balance-Report.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function exportPdf(PaginateRequest $request): mixed
    {
        try {
            $company = $this->companyService->list();
            $copyright   = Settings::group('site')->get('site_copyright');
            $users = $this->userService->list($request);

            $imagePath = ThemeSetting::where(['key' => 'theme_logo'])->first()?->logo;
            $response = Http::withOptions(['verify' => false])->get($imagePath);
            $data = $response->body();
            $theme_logo = 'data:image/png;base64,' . base64_encode($data);

            $pdf = Pdf::loadView('reports.creditBalanceReport', compact('company', 'theme_logo', 'users', 'copyright'))
                ->setPaper('a4')->setOption(['defaultFont' => 'Urbanist']);;
            return response()->stream(
                fn() => print($pdf->output()),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="online_order_report.pdf"',
                ]
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
