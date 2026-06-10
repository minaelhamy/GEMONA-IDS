<?php

use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod:: CASH           => 'Cash',
    PosPaymentMethod:: CARD           => 'Card',
    PosPaymentMethod:: MOBILE_BANKING => 'Mobile Banking',
    PosPaymentMethod:: OTHER          => 'Other',
];