<?php

declare(strict_types=1);

namespace LaminasTest\Http\TestAsset;

use Laminas\Http\Client;

class ExtendedClient extends Client
{
    public const AUTH_CUSTOM = 'custom';
}
