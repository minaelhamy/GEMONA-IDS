<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING    => 'Pending',
    OrderStatus::CONFIRMED  => 'Confirmed',
    OrderStatus::ON_THE_WAY => 'On The Way',
    OrderStatus::DELIVERED  => 'Delivered',
    OrderStatus::CANCELED   => 'Canceled',
    OrderStatus::REJECTED   => 'Rejected',


];