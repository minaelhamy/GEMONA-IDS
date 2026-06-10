<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CompanyService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsReportExport;
use App\Http\Requests\PaginateRequest;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Resources\ProductAdminResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\ProductsReportOverviewResource;

class ProductsReportController extends AdminController implements HasMiddleware
{

    private ProductService $productService;
    private CompanyService $companyService;

    public function __construct(ProductService $productService, CompanyService $companyService)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->companyService = $companyService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:products-report', only: ['index', 'productsReportOverview', 'export', 'exportPdf']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductAdminResource::collection($this->productService->productReport($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function productsReportOverview(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductsReportOverviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductsReportOverviewResource($this->productService->productsReportOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ProductsReportExport($this->productService, $request), 'Product-Report.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function exportPdf(PaginateRequest $request): mixed
    {
        try {
            $company = $this->companyService->list();
            $copyright   = Settings::group('site')->get('site_copyright');
            $products = $this->productService->productReport($request);

            $imagePath = ThemeSetting::where(['key' => 'theme_logo'])->first()?->logo;
            $response = Http::withOptions(['verify' => false])->get($imagePath);
            $data = $response->body();
            $theme_logo = 'data:image/png;base64,' . base64_encode($data);

            $pdf = Pdf::loadView('reports.productReport', compact('company', 'theme_logo', 'products', 'copyright'))
                ->setPaper('a4');
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
