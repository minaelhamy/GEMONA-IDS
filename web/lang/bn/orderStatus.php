<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING    => 'মুলতুবি',
    OrderStatus::CONFIRMED  => 'নিশ্চিত',
    OrderStatus::ON_THE_WAY => 'রাস্তায়',
    OrderStatus::DELIVERED  => 'ডেলিভার',
    OrderStatus::CANCELED   => 'বাতিল',
    OrderStatus::REJECTED   => 'প্রত্যাখ্যাত'
];
