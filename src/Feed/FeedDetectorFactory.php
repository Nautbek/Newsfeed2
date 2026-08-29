<?php

namespace App\Feed;

use App\Exceptions\UnsupportedFeedException;

class FeedDetectorFactory
{
    /**
     * @throws UnsupportedFeedException
     */
    public function createDetector(FeedType $feedType): FeedDetectorInterface
    {
        return match ($feedType) {
            FeedType::Rss      => new FeedRssDetector(),
            FeedType::Atom     => new FeedAtomDetector(),
            FeedType::JsonFeed => new FeedJsonDetector(),
            default            => throw new UnsupportedFeedException()
        };
    }
}
