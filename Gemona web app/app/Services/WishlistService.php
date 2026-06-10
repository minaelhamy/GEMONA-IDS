<?php

namespace App\Services;


use App\Http\Requests\PaginateRequest;
use App\Http\Requests\WishlistRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Tax;
use App\Models\Wishlist;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistService
{
    protected array $wishlistFilter = [
        'product_id'
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

            if (Auth::check()) {
                return Wishlist::where(function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->wishlistFilter)) {
                            $query->where($key, $request);
                        }
                    }
                })->where(['user_id' => Auth::user()->id])->orderBy($orderColumn, $orderType)->$method($methodValue);
            } else {
                return collect([]);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
    /**
     * @throws Exception
     */
    public function toggle(WishlistRequest $request): void
    {
        try {
            $wishlist = Wishlist::where(['product_id' => $request->product_id, 'user_id' => Auth::user()->id])->first();
            if ($request->toggle) {
                if (!$wishlist) {
                    Wishlist::create([
                        'product_id' => $request->product_id,
                        'user_id'    => Auth::user()->id
                    ]);
                }
            } else {
                if ($wishlist) {
                    $wishlist->delete();
                }
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
