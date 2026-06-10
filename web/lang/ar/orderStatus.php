<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING => 'قيد الانتظار',
    OrderStatus::CONFIRMED => 'تم التأكيد',
    OrderStatus::ON_THE_WAY => 'في الطريق',
    OrderStatus::DELIVERED => 'تم التوصيل',
    OrderStatus::CANCELED => 'ألغيت',
    OrderStatus::REJECTED => 'تم رفضه'
];
