<?php

namespace App\Enums;

interface PurchasePaymentStatus
{
    const PENDING      = 5;
    const PARTIAL_PAID = 10;
    const FULLY_PAID   = 15;
}