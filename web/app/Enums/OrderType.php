<?php

namespace App\Enums;

interface OrderType
{
    const DELIVERY = 5;
    const PICK_UP = 10;
    const POS = 15;
}