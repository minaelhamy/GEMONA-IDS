<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Exports\PurchasesExport;
use App\Models\ProductVariation;
use App\Services\PurchaseService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Services\ProductVariationService;
use App\Http\Requests\PurchasePaymentRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\PurchaseDetailsResource;
use App\Http\Resources\PurchasePaymentResource;
use Illuminate\Routing\Controllers\HasMiddleware;

class PurchaseController extends AdminController implements HasMiddleware
{
    public PurchaseService $purchaseService;
    public ProductVariationService $productVariationService;

    public function __construct(PurchaseService $purchaseService, ProductVariationService $productVariationService)
    {
        parent::__construct();
        $this->purchaseService = $purchaseService;
        $this->productVariationService = $productVariationService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:purchase', only: ['index']),
            new Middleware('permission:purchase', only: ['export']),
            new Middleware('permission:purchase', only: ['downloadAttachment']),
            new Middleware('permission:purchase_create', only: ['store']),
            new Middleware('permission:purchase_edit', only: ['edit']),
            new Middleware('permission:purchase_edit', only: ['update']),
            new Middleware('permission:purchase_delete', only: ['destroy']),
            new Middleware('permission:purchase_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  PurchaseResource::collection($this->purchaseService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(PurchaseRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PurchaseResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PurchaseResource($this->purchaseService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PurchaseDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PurchaseDetailsResource($this->purchaseService->show($purchase));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function edit(Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PurchaseDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PurchaseDetailsResource($this->purchaseService->edit($purchase));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function update(PurchaseRequest $request, Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PurchaseResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PurchaseResource($this->purchaseService->update($request, $purchase));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function destroy(Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->purchaseService->destroy($purchase);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new PurchasesExport($this->purchaseService, $request), 'Purchases.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function downloadAttachment(Purchase $purchase)
    {
        try {
            return $this->purchaseService->downloadAttachment($purchase);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function payment(PurchasePaymentRequest $request, Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PurchaseResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PurchaseResource($this->purchaseService->payment($request, $purchase));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function paymentHistory(Purchase $purchase): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  PurchasePaymentResource::collection($this->purchaseService->paymentHistory($purchase));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function paymentDownloadAttachment(PurchasePayment $purchasePayment)
    {
        try {
            return $this->purchaseService->paymentDownloadAttachment($purchasePayment);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function paymentDestroy(Purchase $purchase, PurchasePayment $purchasePayment): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->purchaseService->paymentDestroy($purchase, $purchasePayment);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
