<?php

namespace App\Services;

use App\Enums\Ask;
use Exception;
use App\Models\Stock;
use App\Enums\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use Illuminate\Pagination\LengthAwarePaginator;

class StockService
{
    public $items;
    public $links;
    protected $stockFilter = [
        'product_name',
        'status',
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            $stocks =  Stock::with('product')->where('status', Status::ACTIVE)->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->stockFilter)) {
                        if ($key == "product_name") {
                            $query->whereHas('product', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request . '%');
                            })->get();
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->get();

            if (!blank($stocks)) {
                $stocks->groupBy('product_id')?->map(function ($product) {
                    $product->groupBy('item_id')?->map(function ($item) {
                        $this->items[] = [
                            'product_id'         => $item->first()['product_id'],
                            'product_name'       => $item->first()['product']['name'],
                            'variation_names'    => $item->first()['variation_names'],
                            'status'             => $item->first()['product']['status'],
                            'stock'              => $item->first()['product']['can_purchasable'] === Ask::NO ? "N/C" : $item->sum('quantity'),

                        ];
                    });
                });
            } else {
                $this->items = [];
            }

            if ($method == 'paginate') {
                return $this->paginate($this->items, $methodValue, null, URL::to('/') . '/api/admin/stock');
            }

            return $this->items;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function paginate(
        $items,
        $perPage = 15,
        $page = null,
        $baseUrl = null,
        $options = []
    ) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
}
