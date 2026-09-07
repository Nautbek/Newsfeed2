<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class ImportArticleMessage
{
    public function __construct(public int $feedId)
    {
    }
}
