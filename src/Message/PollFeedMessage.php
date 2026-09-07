<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class PollFeedMessage
{
    public function __construct(public int $feedId)
    {
    }
}
