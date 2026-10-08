<?php

namespace App\Services\Plugin;

use RuntimeException;

class PluginAddressException extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message)
    {
        parent::__construct($message);
    }
}
