<?php

namespace App\Feed\Parser;

use App\Feed\FeedType;

class FeedParserFactory
{
    public function build(FeedType $feedType) : FeedParserInterface
    {
        return match ($feedType) {
            FeedType::JsonFeed => new FeedJsonParser(),
            FeedType::Rss => new FeedRss2Parser(),
            FeedType::Atom => new FeedAtomParser(),
        };
    }
}
