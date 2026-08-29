<?php

namespace App\Feed\Parser;

use App\Dto\ParsedArticle;
use App\Dto\ParsedFeed;
use App\Exceptions\UnsupportedFeedException;
use App\Feed\FeedType;

class FeedRss2Parser implements FeedParserInterface
{
    /**
     * @throws UnsupportedFeedException
     * @throws \DateMalformedStringException
     */
    public function parse(string $json): ParsedFeed
    {
        $xml = @simplexml_load_string($json);

        if ($xml === false || !isset($xml->channel)) {
            throw new UnsupportedFeedException('Rss parse error');
        }

        $channel = $xml->channel;

        $feed = new ParsedFeed();
        $feed->title = isset($channel->title) ? (string) $channel->title : null;
        $feed->url = isset($channel->link) ? (string) $channel->link : null;
        $feed->type = FeedType::Rss->value;

        $articles = [];

        foreach ($channel->item as $item) {
            $guid = isset($item->guid) ? (string) $item->guid : null;
            $link = isset($item->link) ? (string) $item->link : null;
            $key = $guid ?: $link;

            $newArticle = new ParsedArticle();
            $newArticle->title = (string) ($item->title ?? '');
            $newArticle->summary = (string) ($item->description ?? '');
            $newArticle->publishedAt = new \DateTimeImmutable((string) ($item->pubDate ?? 'now'));
            $newArticle->url = (string) ($item->link ?? '');
            $newArticle->guid = $key;
            $newArticle->feed = $feed;

            $articles[] = $newArticle;
        }

        $feed->articles = $articles;

        return $feed;
    }
}
