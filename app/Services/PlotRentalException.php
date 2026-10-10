<?php

namespace App\Services;

use RuntimeException;

/** A rental action that could not go through, with a message for the player. */
class PlotRentalException extends RuntimeException
{
    public function __construct(public readonly string $title, string $message)
    {
        parent::__construct($message);
    }

    public function flash(): array
    {
        return ['title' => $this->title, 'message' => $this->getMessage()];
    }
}
