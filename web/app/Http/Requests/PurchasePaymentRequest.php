<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Foundation\Http\FormRequest;

class PurchasePaymentRequest extends FormRequest
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
            'purchase_id'    => ['required', 'not_in:0', 'not_in:null'],
            'date'           => ['required', 'string'],
            'reference_no'   => ['nullable', 'string'],
            'amount'         => ['required', 'numeric'],
            'payment_method' => ['required', 'not_in:0', 'not_in:null'],
            'file'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $status = false;
            $message  = '';

            $purchasePaymentAmount = PurchasePayment::where('purchase_id', request('purchase_id'))->sum('amount');
            $purchase = Purchase::findOrFail(request('purchase_id'));

            $paymentDue = (float) $purchase->total - (float)$purchasePaymentAmount;

            if ($paymentDue < request('amount')) {
                $status = true;
                $message = trans('all.message.price_total_invalid');
            }

            if ($status) {
                $validator->errors()->add('global', $message);
            }
        });
    }
}