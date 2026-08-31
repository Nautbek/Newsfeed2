<?php

namespace App\Feed;

use App\Exceptions\UnsupportedFeedException;

class FeedTypeDetector
{
    private const string CONTENT_TYPE_JSON = 'application/json';
    private const string CONTENT_TYPE_ATOM = 'application/atom+xml';
    private const string CONTENT_TYPE_RSS = 'application/rss+xml';

    /**
     * @throws UnsupportedFeedException
     */
    public function detect(string $body, ?string $contentType): FeedType
    {
        $feedTypes = FeedType::cases();

        $startContentType = self::getFeedType($contentType);

        if ($startContentType instanceof FeedType) {
            array_unshift($feedTypes, $startContentType); // Начинаем проверку с заголовка.
        }

        $feedDetectorFactory = new FeedDetectorFactory();

        foreach ($feedTypes as $feedType) {
            $detector = $feedDetectorFactory->createDetector($feedType);
            if ($detector->detect($body)) {
                return $feedType;
            }
        }

        throw new UnsupportedFeedException('Не удалось определить тип фида');
    }

    /**
     * @param string|null $contentType
     * @return FeedType|null
     */
    public static function getFeedType(?string $contentType): ?FeedType
    {
        $feedTypes = [
            self::CONTENT_TYPE_RSS  => FeedType::Rss,
            self::CONTENT_TYPE_JSON => FeedType::JsonFeed,
            self::CONTENT_TYPE_ATOM => FeedType::Atom,
        ];

        return $feedTypes[$contentType] ?? null;
    }
}
