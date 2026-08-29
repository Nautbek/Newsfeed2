<?php

namespace App\Feed\Parser;

use App\Dto\ParsedArticle;
use App\Dto\ParsedFeed;
use App\Entity\Source;
use App\Exceptions\UnsupportedFeedException;
use App\Feed\FeedType;

class FeedJsonParser implements FeedParserInterface
{
    private const SUPPORTED_VERSION = 'https://jsonfeed.org/version/1.1';

    /**
     * @throws UnsupportedFeedException
     * @throws \DateMalformedStringException
     */
    public function parse(string $json): ParsedFeed
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new UnsupportedFeedException('Json parse error: ' . json_last_error_msg());
        }

        $version = $data['version'] ?? null;
        if (!is_string($version)) {
            throw new UnsupportedFeedException('Json version detect error');
        }

        if ($version !== self::SUPPORTED_VERSION || !isset($data['items'])) {
            throw new UnsupportedFeedException('Json version unsupported');
        }

        $items = $data['items'];

        $articles = [];

        $feed = new ParsedFeed();
        $feed->title = $data['title'] ?? null;
        $feed->url = $data['url'] ?? null;
        $feed->type = FeedType::JsonFeed->value;

        foreach ($items as $item) {
            $guid = $item['id'] ?? null;
            $link = $item['url'] ?? null;
            $key = $guid ?? $link;

            $newArticle = new ParsedArticle();
            $newArticle->title = (string) ($item['title'] ?? null);
            $newArticle->summary = (string) ($item['description'] ?? null);
            $newArticle->publishedAt = new \DateTimeImmutable(($item['date_published'] ?? null));
            $newArticle->url = (string) ($item['url'] ?? null);
            $newArticle->guid = $key;
            $newArticle->feed = $feed;

            $articles[] = $newArticle;
        }

        $feed->articles = $articles;

        return $feed;
    }
}
