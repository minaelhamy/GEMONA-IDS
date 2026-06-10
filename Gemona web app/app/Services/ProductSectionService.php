<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ProductSection;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ProductSectionRequest;
use App\Libraries\QueryExceptionLibrary;

class ProductSectionService
{
    protected array $productCateFilter = [
        'name',
        'slug',
        'status',
    ];

    protected array $exceptFilter = [
        'excepts'
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

            return ProductSection::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productCateFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
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
    public function store(ProductSectionRequest $request)
    {
        try {
            return ProductSection::create($request->validated() + ['slug' => Str::slug($request->name)]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductSectionRequest $request, ProductSection $productSection): ProductSection
    {
        try {
            $productSection->update($request->validated() + ['slug' => Str::slug($request->name)]);
            return $productSection;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ProductSection $productSection): void
    {
        try {
            $productSection->productSectionProducts()->delete();
            $productSection->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ProductSection $productSection): ProductSection
    {
        try {
            return $productSection;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function productSectionWithProduct()
    {
        try {
            return ProductSection::select('product_sections.id', 'product_sections.name', 'product_sections.slug', 'product_sections.status')->with(['products' => function ($query) {
                $query->select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status')
                    ->with(['wishlist' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                    ->withReviewRating()
                    ->with('media', 'variations', 'reviews')
                    ->active('products.status')
                    ->whereNull('deleted_at');
            }])->active('product_sections.status')->orderBy('id', 'asc')->get()->map(function ($query) {
                $query->setRelation('products', $query->products->take(8));
                return $query;
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
