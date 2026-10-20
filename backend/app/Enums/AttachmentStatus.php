<?php

namespace App\Enums;

enum AttachmentStatus : string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public const array VALUES = [
        self::Pending->value,
        self::Processing->value,
        self::Completed->value,
        self::Failed->value,
    ];
}
