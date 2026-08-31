<?php

namespace App\Feed\Parser;

use App\Dto\ParsedFeed;

interface FeedParserInterface
{
    public function parse(string $json): ParsedFeed;

    public static function getSupportedType(): string;
}
