<?php

namespace App\Feed\Parser;

use App\Exceptions\UnsupportedFeedException;

final readonly class FeedParserRegistry
{
    public function __construct(private array $parsers)
    {

    }

    public function get(string $type): FeedParserInterface
    {
        if (!isset($this->parsers[$type])) {
            throw new UnsupportedFeedException();
        }

        return $this->parsers[$type];
    }

    public function supportedTypes(): array
    {
        return array_keys($this->parsers);
    }
}
