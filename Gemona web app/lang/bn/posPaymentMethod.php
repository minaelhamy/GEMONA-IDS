<?php

use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'নগদ',
    PosPaymentMethod::CARD           => 'কার্ড',
    PosPaymentMethod::MOBILE_BANKING => 'মোবাইল ব্যাংকিং',
    PosPaymentMethod::OTHER          => 'অন্যান্য',
];
