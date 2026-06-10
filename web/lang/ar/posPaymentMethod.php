<?php

use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'نقدي',
    PosPaymentMethod::CARD           => 'بطاقة',
    PosPaymentMethod::MOBILE_BANKING => 'الخدمات المصرفية عبر الهاتف المحمول',
    PosPaymentMethod::OTHER          => 'أخرى',
];
