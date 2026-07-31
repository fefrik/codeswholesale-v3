<?php

namespace CodesWholesaleApi\Enum;

enum OrderStatus: string
{
    case Pending = 'PENDING';
    case Processing = 'PROCESSING';
    case Ready = 'READY';
    case Prepared = 'PREPARED';
    case Completed = 'COMPLETED';
    case Fulfilled = 'FULFILLED';
    case Failed = 'FAILED';
    case Cancelled = 'CANCELLED';
}
