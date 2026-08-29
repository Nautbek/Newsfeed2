<?php

namespace App\Feed;

use App\Exceptions\UnsupportedFeedException;
use InvalidArgumentException;

class FeedRssDetector implements FeedDetectorInterface
{
    private const string RSS2_START_BLOCK = '<rss';
    private const string RSS1_START_BLOCK = '<rdf:RDF';

    /**
     * @throws UnsupportedFeedException
     */
    public function detect(string $body): bool
    {
        $result = false;

        if (str_contains($body, self::RSS1_START_BLOCK)) {
            throw new UnsupportedFeedException(message: 'RSS 1.0 Unsupported');
        }

        if (str_contains($body, self::RSS2_START_BLOCK)) {

            $xml = simplexml_load_string($body);

            if ($xml === false) {
                throw new InvalidArgumentException('Invalid XML');
            }

            $result = true;
        }

        return $result;
    }
}
