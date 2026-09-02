<?php

namespace App\Feed\Parser;

use App\Dto\ParsedArticle;
use App\Dto\ParsedFeed;
use App\Exceptions\UnsupportedFeedException;
use App\Feed\FeedType;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [['name' => 'app.feed_parser', 'priority' => 10]])]
class FeedAtomParser implements FeedParserInterface
{
    /**
     * @throws UnsupportedFeedException
     * @throws \DateMalformedStringException
     */
    public function parse(string $json): ParsedFeed
    {
        $xml = @simplexml_load_string($json);

        if ($xml === false || $xml->getName() !== 'feed') {
            throw new UnsupportedFeedException('Atom parse error');
        }

        $feed = new ParsedFeed();
        $feed->title = isset($xml->title) ? (string) $xml->title : null;
        $feed->url = $this->extractLink($xml);
        $feed->type = FeedType::Atom->value;

        $articles = [];

        foreach ($xml->entry as $entry) {
            $link = $this->extractLink($entry);
            $id = isset($entry->id) ? (string) $entry->id : null;
            $key = $id ?: $link;

            $date = (string) ($entry->updated ?? $entry->published ?? 'now');

            $newArticle = new ParsedArticle();
            $newArticle->title = (string) ($entry->title ?? '');
            $newArticle->summary = (string) ($entry->summary ?? $entry->content ?? '');
            $newArticle->publishedAt = new \DateTimeImmutable($date ?: 'now');
            $newArticle->url = (string) $link;
            $newArticle->guid = $key;
            $newArticle->feed = $feed;

            $articles[] = $newArticle;
        }

        $feed->articles = $articles;

        return $feed;
    }

    private function extractLink(\SimpleXMLElement $element): ?string
    {
        foreach ($element->link as $link) {
            $rel = (string) ($link['rel'] ?? 'alternate');
            if ($rel === 'alternate' || $rel === '') {
                return (string) $link['href'];
            }
        }

        return isset($element->link[0]) ? (string) $element->link[0]['href'] : null;
    }

    public static function getSupportedType(): string
    {
        return FeedType::Atom->value;
    }
}
