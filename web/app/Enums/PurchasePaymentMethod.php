<?php

namespace App\Enums;

interface PurchasePaymentMethod
{
    const CASH        = 5;
    const CHEQUE      = 10;
    const CREDIT_CARD = 15;
    const OTHERS      = 20;
}