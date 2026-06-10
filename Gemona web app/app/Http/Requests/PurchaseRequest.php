<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id'   => ['required','not_in:0','not_in:null'],
            'date'          => ['required','string'],
            'status'        => ['required','not_in:0','not_in:null'],
            'total'         => ['required', 'numeric'],
            'file'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'note'          => ['nullable', 'string', 'max:1000'],
            'products'      => ['required','json']
                ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $status = false;
            $message  = '';
            $products = json_decode($this->products, true);
            if (is_array($products) && count($products)) {
                foreach ($products as $product) {
                    if ($product['quantity'] < 1 || !is_numeric($product['quantity']) || !is_int((int) $product['quantity'])) {
                        $status = true;
                        $message = trans('all.message.product_quantity_invalid');
                    } else if (!is_numeric($product['price']) || !is_double((float) $product['price']) || $product['price'] == 0 || $product['price'] < 0) {
                        $status = true;
                        $message = trans('all.message.product_price_invalid');
                    } else if (!is_numeric($product['total']) || !is_double((float) $product['total'])) {
                        $status = true;
                        $message = trans('all.message.product_price_total_invalid');
                    }
                }
            } else {
                $validator->errors()->add('products', trans('all.message.product_invalid'));
            }

            if ($status) {
                $validator->errors()->add('global', $message);
            }
        });
    }

    public function attributes()
    {
        return [
            'supplier_id' => 'supplier'
        ];
    }
}
