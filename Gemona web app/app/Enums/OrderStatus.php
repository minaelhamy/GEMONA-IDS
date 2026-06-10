<?php

namespace App\Enums;

interface OrderStatus
{
    const PENDING = 1;
    const CONFIRMED = 5;
    const ON_THE_WAY = 7;
    const DELIVERED = 10;
    const CANCELED = 15;
    const REJECTED = 20;
}
