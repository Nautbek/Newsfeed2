<?php

namespace App\MessageHandler;

use App\Message\PollDueFeedsMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PollDueFeedsMessageHandler
{
    public function __invoke(PollDueFeedsMessage $message): void
    {
        // do something with your message
    }
}
