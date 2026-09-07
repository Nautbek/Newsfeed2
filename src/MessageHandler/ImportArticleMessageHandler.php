<?php

namespace App\MessageHandler;

use App\Message\ImportArticleMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ImportArticleMessageHandler
{
    public function __invoke(ImportArticleMessage $message): void
    {
        // do something with your message
    }
}
