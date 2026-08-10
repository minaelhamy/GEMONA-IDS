<?php

namespace App\Services;


use Exception;
use App\Enums\Ask;
use Carbon\Carbon;
use App\Enums\Status;
use App\Models\Product;
use App\Enums\BarcodeType;
use App\Models\ProductTag;
use App\Models\ProductTax;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Libraries\AppLibrary;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\PaginateRequest;
use Picqer\Barcode\BarcodeGeneratorJPG;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ProductOfferRequest;
use App\Http\Requests\ShippingAndReturnRequest;
use App\Libraries\QueryExceptionLibrary;

class ProductService
{
    public object $product;
    protected array $productFilter = [
        'name',
        'sku',
        'slug',
        'buying_price',
        'selling_price',
        'product_category_id',
        'product_brand_id',
        'barcode_id',
        'tax_id',
        'unit_id',
        'show_stock_out',
        'status',
        'can_purchasable',
        'refundable',
        'weight',
        'order',
        'except'
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

            return Product::with('media', 'category', 'brand', 'taxes', 'tags', 'reviews')->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])->withReviewRating()->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productFilter)) {
                        if ($key == "except") {
                            $explodes = explode('|', $request);
                            if (count($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('id', '!=', $explode);
                                }
                            }
                        } else {
                            if ($key == "product_category_id") {
                                $query->where($key, $request);
                            } elseif ($key == "tax_id") {
                                $query->whereHas('taxes', function ($q) use ($key, $request) {
                                    $q->where($key, $request);
                                });
                            } else {
                                $query->where($key, 'like', '%' . $request . '%');
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ProductRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->product = Product::create($request->validated() + ['slug' => Str::slug($request->name), 'variation_price' => $request->selling_price]);
                if ($request->tags) {
                    $tagItems = json_decode($request->tags, true);
                    foreach ($tagItems as $tagItem) {
                        ProductTag::create([
                            'product_id' => $this->product->id,
                            'name'       => $tagItem['text']
                        ]);
                    }
                }

                if ($request->tax_id) {
                    foreach ($request->tax_id as $tax) {
                        ProductTax::create([
                            'product_id' => $this->product->id,
                            'tax_id'     => $tax
                        ]);
                    }
                }

                $this->attachBarcodeImage($this->product, $request->barcode_id, $request->sku);
            });
            return $this->product;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductRequest $request, Product $product): object
    {
        try {
            DB::transaction(function () use ($request, $product) {
                if ($request->barcode_id != $product->barcode_id || $request->sku != $product->sku) {
                    $product->update($request->validated() + ['slug' => Str::slug($request->name)]);

                    $product->clearMediaCollection('product-barcode');
                    $this->attachBarcodeImage($product, $request->barcode_id, $request->sku);
                } else {
                    $product->update($request->validated() + ['slug' => Str::slug($request->name)]);
                }

                if ($request->tags) {
                    $product->tags()->delete();
                    $tagItems = json_decode($request->tags, true);
                    foreach ($tagItems as $tagItem) {
                        ProductTag::create([
                            'product_id' => $product->id,
                            'name'       => $tagItem['text']
                        ]);
                    }
                }

                if ($request->tax_id) {
                    $product->taxes()->delete();
                    foreach ($request->tax_id as $tax) {
                        ProductTax::create([
                            'product_id' => $product->id,
                            'tax_id'     => $tax
                        ]);
                    }
                }

                if (!$request->tax_id) {
                    $product->taxes()->delete();
                }

                if ($product->variations) {
                    $this->product = Product::find($product->id);
                    $checkMinPrice = $product->variations->min('price');
                    if ($checkMinPrice) {
                        $this->product->variation_price = $checkMinPrice;
                        $this->product->save();
                    }
                }
            });
            return $this->product;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function downloadBarcode(Product $product)
    {
        return $product->getMedia('product-barcode')->first();
    }

    private function attachBarcodeImage(Product $product, int|string|null $barcodeId, string $sku): void
    {
        $barcode = $this->barcodeImage($barcodeId, $sku);
        if ($barcode === null) {
            return;
        }

        $directory = storage_path('app/public/barcodes');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $tempFilePath = $directory . DIRECTORY_SEPARATOR . Str::uuid() . '.jpg';
        file_put_contents($tempFilePath, $barcode);
        $product->addMedia($tempFilePath)->toMediaCollection('product-barcode');
    }

    private function barcodeImage(int|string|null $barcodeId, string $sku): ?string
    {
        $generator = new BarcodeGeneratorJPG();

        if ((int) $barcodeId === BarcodeType::EAN_13) {
            return $generator->getBarcode(str_pad($sku, 12, '0', STR_PAD_LEFT), $generator::TYPE_EAN_13);
        }

        if ((int) $barcodeId === BarcodeType::UPC_A) {
            return $generator->getBarcode(str_pad($sku, 11, '0', STR_PAD_LEFT), $generator::TYPE_UPC_A);
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function destroy(Product $product): void
    {
        try {
            DB::transaction(function () use ($product) {
                if ($product->productTaxes) {
                    $product->productTaxes()->delete();
                }
                if ($product->wishlist) {
                    $product->wishlist()->delete();
                }
                $product->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Product $product): Product
    {
        try {
            return $product;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function barcodeProduct($barcode)
    {
        try {
            $productVariation = ProductVariation::where(['sku' => $barcode])->first();
            $product = Product::where(['sku' => $barcode])->first();

            if ($productVariation || $product) {
                if ($productVariation) {
                    return [
                        'product_id'   => $productVariation->product_id,
                        'variation_id' => $productVariation->id
                    ];
                }
                if ($product) {
                    return [
                        'product_id'   => $product->id,
                        'variation_id' => ''
                    ];
                }
            } else {
                throw new Exception(trans('all.message.product_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function uploadImage(ChangeImageRequest $request, Product $product): Product
    {
        try {
            $product->addMedia($request->image)->toMediaCollection('product');
            return $product;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deleteImage(Product $product, $index): Product
    {
        try {
            $images = $product->getMedia('product');
            if (isset($images[$index])) {
                $images[$index]->delete();
            }
            return Product::find($product->id);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function mostPopularProducts(PaginateRequest $request)
    {
        try {

            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 32) : '*';
            $limit       = max(1, min(32, (int)$request->get('limit', $request->get('per_page', 8))));
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $rand        = $request->get('rand', 0) > 0 ? $request->get('rand') : 0;

            $query = Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with('media', 'variations')
                ->withReviewRating()
                ->withDisplayImage()
                ->where(['status' => Status::ACTIVE]);

            if ($rand > 0) {
                return $query->orderByDesc('products.id')->limit($rand)->get();
            }

            return $query
                ->withCount('orderCountable')
                ->orderBy('order_countable_count', 'desc')
                ->orderBy($orderColumn, $orderType)
                ->when($method === 'get', fn($query) => $query->limit($limit))
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function productReport(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'asc';
            return Product::withCount('orders')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate(
                        'created_at',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productFilter)) {
                        if ($key == "product_category_id") {
                            $query->where($key, $request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function productsReportOverview(Request $request)
    {
        try {
            $requests    = $request->all();
            $products =  Product::withSum('productOrders', 'quantity')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate(
                        'created_at',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productFilter)) {
                        if ($key == "product_category_id") {
                            $query->where($key, $request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            })->get();

            $productsReportArray = [];

            $productsReportArray['total_products']      = $products->count();
            $productsReportArray['total_categories']    = $products->groupBy('product_category_id')->count();
            $productsReportArray['total_sold_quantity'] = abs($products->sum('product_orders_sum_quantity'));

            return $productsReportArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function generateSku()
    {
        try {
            return AppLibrary::sku(rand(1000000, 9999999));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function shippingAndReturn(ShippingAndReturnRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $product->update($request->validated());
            });
            return Product::find($product->id);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
        }
    }

    /**
     * @throws Exception
     */
    public function productOffer(ProductOfferRequest $request, Product $product): object
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $this->product              = $product;
                $product->add_to_flash_sale = $request->add_to_flash_sale;
                $product->discount          = $request->discount;
                $product->offer_start_date  = date('Y-m-d H:i:s', strtotime($request->offer_start_date));
                $product->offer_end_date    = date('Y-m-d H:i:s', strtotime($request->offer_end_date));
                $product->save();
            });
            return $this->product;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function categoryWiseProducts(Request $request)
    {
        try {
            $customProductFilter = [
                'name',
                'sku',
                'slug',
                'status'
            ];

            $customProductFilterMask = [
                'name'   => 'products.name',
                'sku'    => 'products.sku',
                'slug'   => 'products.slug',
                'status' => 'products.status'
            ];

            $categories = [];
            $categoryIds = [];
            if ($request->has('category')) {
                if (!blank($request->category)) {
                    $categories = ProductCategory::where(['slug' => $request->category])->first();
                    if ($categories) {
                        $categories = $categories->descendantsAndSelf->toArray();
                        $categoryIds = collect($categories)->pluck('id')->all();
                    } else {
                        $categories = [];
                    }
                }
            }

            $baseQuery = Product::query()
                ->select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.status', 'products.product_category_id', 'products.product_brand_id', 'products.variation_price', 'products.external_image_url')
                ->active('products.status')
                ->withDisplayImage()
                ->when(count($categoryIds), function ($query) use ($categoryIds) {
                    $query->whereIn('product_category_id', $categoryIds);
                })
                ->where(function ($query) use ($request, $customProductFilter, $customProductFilterMask) {
                foreach ($request->all() as $key => $req) {
                    if (in_array($key, $customProductFilter) && !blank($req)) {
                        $query->where($customProductFilterMask[$key], 'like', '%' . $req . '%');
                    }
                }
            });

            $brands = ProductBrand::query()
                ->select('product_brands.id', 'product_brands.name')
                ->join('products', 'products.product_brand_id', '=', 'product_brands.id')
                ->whereNotNull('products.product_brand_id')
                ->where('products.status', Status::ACTIVE)
                ->when(count($categoryIds), function ($query) use ($categoryIds) {
                    $query->whereIn('products.product_category_id', $categoryIds);
                })
                ->where(function ($query) use ($request, $customProductFilter, $customProductFilterMask) {
                    foreach ($request->all() as $key => $req) {
                        if (in_array($key, $customProductFilter) && !blank($req)) {
                            $query->where($customProductFilterMask[$key], 'like', '%' . $req . '%');
                        }
                    }
                })
                ->distinct()
                ->orderBy('product_brands.name')
                ->limit(200)
                ->get();

            $maxPrice = (clone $baseQuery)->max('products.variation_price');

            $perPage     = $request->post('per_page', 30);
            $orderColumn = 'products.name';
            $orderType   = 'asc';
            if ($request->post('sort_by') == 'newest') {
                $orderColumn = 'id';
                $orderType   = 'desc';
            } else if ($request->post('sort_by') == 'price_low_to_high') {
                $orderColumn = 'products.variation_price';
            } else if ($request->post('sort_by') == 'price_high_to_low') {
                $orderColumn = 'products.variation_price';
                $orderType   = 'desc';
            } else if ($request->post('sort_by') == 'top_rated') {
                $orderColumn = 'rating_star';
                $orderType   = 'desc';
            }

            $products = Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.product_category_id', 'products.product_brand_id', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                ->withReviewRating()
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with('media', 'brand', 'variations', 'reviews')
                ->active('products.status')
                ->withDisplayImage()
                ->when(count($categoryIds), function ($query) use ($categoryIds) {
                    $query->whereIn('product_category_id', $categoryIds);
                })
                ->where(function ($query) use ($request) {
                    if (!blank($request->brand)) {
                        $brands = json_decode($request->brand);
                        if (count($brands)) {
                            $query->whereIn('product_brand_id', $brands);
                        }
                    }
                })->where(function ($query) use ($request, $customProductFilter, $customProductFilterMask) {
                    foreach ($request->all() as $key => $req) {
                        if (in_array($key, $customProductFilter) && !blank($req)) {
                            $query->where($customProductFilterMask[$key], 'like', '%' . $req . '%');
                        }
                    }
                })->where(function ($query) use ($request) {
                    if (!blank($request->variation)) {
                        $variations = json_decode($request->variation);
                        if (count($variations)) {
                            $arrays = [];
                            foreach ($variations as $variation) {
                                $arrays[$variation->attribute][] = [
                                    'option' => $variation->option
                                ];
                            }

                            foreach ($arrays as $key => $array) {
                                $query->whereHas('variations', function ($q) use ($key, $array) {
                                    $i = 0;
                                    foreach ($array as $a) {
                                        if ($i === 0) {
                                            $q->where('product_attribute_id', $key)->where('product_attribute_option_id', $a['option']);
                                        } else {
                                            $q->orWhere('product_attribute_id', $key)->where('product_attribute_option_id', $a['option']);
                                        }
                                        $i++;
                                    }
                                });
                            }
                        }
                    }
                })->orderBy($orderColumn, $orderType)->where(function ($query) use ($request) {
                    if ($request->min_price >= 0 && $request->max_price > 0) {
                        $query->whereBetween('variation_price', [$request->min_price, $request->max_price]);
                    }
                })->paginate($perPage);

            return collect([
                'products'   => $products,
                'brands'     => $brands,
                'variations' => [],
                'max_price'  => ceil(((float) $maxPrice) + 50),
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function flashSaleProducts(PaginateRequest $request)
    {
        try {
            $now         = Carbon::now();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 32) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $rand        = $request->get('rand', 0) > 0 ? $request->get('rand') : 0;

            return Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                ->withReviewRating()
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with('media', 'variations', 'reviews')
                ->active('products.status')
                ->withDisplayImage()
                ->where('products.add_to_flash_sale', Ask::YES)
                ->where('products.offer_start_date', '<=', $now)
                ->where('products.offer_end_date', '>=', $now)
                ->randAndLimitOrOrderBy($rand, $orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function offerProducts(PaginateRequest $request)
    {
        try {
            $now         = Carbon::now();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 32) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $rand        = $request->get('rand', 0) > 0 ? $request->get('rand') : 0;

            return Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                ->withReviewRating()
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with('media', 'variations', 'reviews')
                ->active('products.status')
                ->withDisplayImage()
                ->where('products.offer_start_date', '<=', $now)
                ->where('products.offer_end_date', '>=', $now)
                ->randAndLimitOrOrderBy($rand, $orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function showWithRelation(Product $product, Request $request)
    {
        try {
            return Product::with('media', 'videos', 'category', 'unit', 'taxes')
                ->with(['seo' => fn($query) => $query->with('media')])
                ->withSum('stockItems', 'quantity')
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with(['reviews' => fn($query) => $query->with('user', 'media')->take($request->get('review_limit', 3))])
                ->withReviewRating()
                ->where(['id' => $product->id, 'status' => Status::ACTIVE])
                ->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function showWithTrashed(Product $product, Request $request)
    {
        try {
            return Product::with('media', 'videos', 'category', 'unit', 'taxes')
                ->with(['seo' => fn($query) => $query->with('media')])
                ->withSum('stockItems', 'quantity')
                ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->with(['reviews' => fn($query) => $query->with('user', 'media')->take($request->get('review_limit', 3))])
                ->withReviewRating()
                ->where(['id' => $product->id, 'status' => Status::ACTIVE])
                ->withTrashed()
                ->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function relatedProducts(Product $product, PaginateRequest $request)
    {
        try {
            $productTags = $product->tags;
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 32) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $rand        = $request->get('rand', 0) > 0 ? $request->get('rand') : 0;

            if (count($productTags) > 0) {
                return Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                    ->withReviewRating()
                    ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                    ->with('media', 'variations', 'reviews', 'tags')
                    ->active('products.status')
                    ->withDisplayImage()
                    ->whereHas('tags', function ($query) use ($productTags) {
                        if (count($productTags) > 0) {
                            $i = 0;
                            foreach ($productTags as $productTag) {
                                if ($i === 0) {
                                    $query->where('name', 'like', '%' . $productTag->name . '%');
                                } else {
                                    $query->orWhere('name', 'like', '%' . $productTag->name . '%');
                                }
                                $i++;
                            }
                        }
                        return $query;
                    })
                    ->whereNot('id', $product->id)
                    ->randAndLimitOrOrderBy($rand, $orderColumn, $orderType)
                    ->$method($methodValue);
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
    public function wishlistProducts(PaginateRequest $request)
    {
        try {
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 32) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $rand        = $request->get('rand', 0) > 0 ? $request->get('rand') : 0;

            return Product::select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status', 'products.external_image_url')
                ->withReviewRating()
                ->with('media', 'variations', 'reviews', 'wishlist')
                ->whereHas('wishlist', function ($query) {
                    return $query->where('user_id', Auth::user()->id);
                })
                ->active('products.status')
                ->withDisplayImage()
                ->randAndLimitOrOrderBy($rand, $orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function purchasableProducts()
    {
        try {
            return Product::select('id', 'name', 'buying_price', 'can_purchasable', 'status', 'sku')
                ->with('productTaxes')
                ->with('variations')
                ->where('can_purchasable', ASK::YES)
                ->where('status', Status::ACTIVE)
                ->orderBy('name', 'asc')
                ->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function simpleProducts()
    {
        try {
            return Product::select('id', 'name', 'buying_price', 'status', 'sku')
                ->with('productTaxes')
                ->with('variations')
                ->orderBy('name', 'asc')
                ->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function topProducts()
    {
        try {
            return Product::withCount('orderCountable')->where(['status' => Status::ACTIVE])->orderBy('order_countable_count', 'desc')->limit(12)->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function ancestorCategoryWiseProducts(ProductCategory $category, $rand = null)
    {

        try {
            $categories = [];
            if (!blank($category)) {
                $categories = ProductCategory::where(['id' => $category->id])->first();
                if ($categories) {
                    $categories = $categories->descendantsAndSelf->toArray();
                } else {
                    $categories = [];
                }
            }

            return Product::select('id', 'name', 'sku', 'slug', 'status', 'product_category_id')
                ->where(function ($query) use ($categories) {
                    if (count($categories)) {
                        $i = 0;
                        foreach ($categories as $category) {
                            if ($i === 0) {
                                $query->where('product_category_id', $category['id']);
                            } else {
                                $query->orWhere('product_category_id', $category['id']);
                            }
                            $i++;
                        }
                    }
                })
                ->randAndLimitOrOrderBy($rand, 'id', 'asc')
                ->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function clearOffer(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                $this->product              = $product;
                $product->add_to_flash_sale = Ask::NO;
                $product->discount          = 0;
                $product->offer_start_date  = null;
                $product->offer_end_date    = null;
                $product->save();
            });
            return $this->product;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
