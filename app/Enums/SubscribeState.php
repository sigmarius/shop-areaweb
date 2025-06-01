<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscribeState: string
{
    case Subscribed = 'subscribed';

    case Unsubscribed = 'unsubscribed';
}
