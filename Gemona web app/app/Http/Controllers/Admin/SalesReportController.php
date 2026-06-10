<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CompanyService;
use App\Exports\SalesReportExport;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Resources\SimpleOrderResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\SalesReportOverviewResource;

class SalesReportController extends AdminController implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private OrderService $orderService;
    private CompanyService $companyService;

    public function __construct(OrderService $orderService, CompanyService $companyService)
    {
        parent::__construct();
        $this->orderService = $orderService;
        $this->companyService = $companyService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:sales-report', only: ['index']),
            new Middleware('permission:sales-report', only: ['salesReportOverview']),
            new Middleware('permission:sales-report', only: ['export']),
            new Middleware('permission:sales-report', only: ['exportPdf']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleOrderResource::collection($this->orderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function salesReportOverview(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SalesReportOverviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SalesReportOverviewResource($this->orderService->salesReportOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new SalesReportExport($this->orderService, $request), 'Sales-Report.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function exportPdf(PaginateRequest $request): mixed
    {
        try {
            $company = $this->companyService->list();
            $copyright   = Settings::group('site')->get('site_copyright');
            $orders = $this->orderService->list($request);

            $imagePath = ThemeSetting::where(['key' => 'theme_logo'])->first()?->logo;
            $response = Http::withOptions(['verify' => false])->get($imagePath);
            $data = $response->body();
            $theme_logo = 'data:image/png;base64,' . base64_encode($data);

            $pdf = Pdf::loadView('reports.salesReport', compact('company', 'theme_logo', 'orders', 'copyright'))
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
