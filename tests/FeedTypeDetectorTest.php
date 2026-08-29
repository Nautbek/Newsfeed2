<?php

namespace App\Tests;

use App\Feed\FeedAtomDetector;
use App\Feed\FeedJsonDetector;
use App\Feed\FeedRssDetector;
use App\Feed\FeedType;
use App\Feed\FeedTypeDetector;
use App\Feed\Parser\FeedAtomParser;
use App\Feed\Parser\FeedJsonParser;
use App\Feed\Parser\FeedRss2Parser;
use PHPUnit\Framework\TestCase;

class FeedTypeDetectorTest extends TestCase
{
    const string JSON_FIXTURE = '/fixtures/feeds/daringfireball.json';
    const string RSS_FIXTURE = '/fixtures/feeds/hackernews.rss.xml';
    const string ATOM_FIXTURE = '/fixtures/feeds/nasa-breaking.atom.xml';

    public function testJsonFeedTypeDetecting(): void
    {
        if (!file_exists(__DIR__ . self::JSON_FIXTURE)) {
            $this->fail('Json feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::JSON_FIXTURE);

        $detector = new FeedJsonDetector();

        $this->assertTrue($detector->detect($body), 'JSON Detection failed');
    }

    public function testRssFeedTypeDetecting(): void
    {
        if (!file_exists(__DIR__ . self::RSS_FIXTURE)) {
            $this->fail('Rss feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::RSS_FIXTURE);

        $detector = new FeedRssDetector();

        $this->assertTrue($detector->detect($body), 'RSS Detection failed');
    }

    public function testAtomFeedTypeDetecting(): void
    {
        if (!file_exists(__DIR__ . self::ATOM_FIXTURE)) {
            $this->fail('Atom feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::ATOM_FIXTURE);

        $detector = new FeedAtomDetector();

        $this->assertTrue($detector->detect($body), 'RSS Detection failed');
    }

    public function testJsonFeedTypeDetection(): void
    {
        if (!file_exists(__DIR__ . self::JSON_FIXTURE)) {
            $this->fail('Json feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::JSON_FIXTURE);

        $detectorFactory = new FeedTypeDetector();
        $feedType = $detectorFactory->detect($body, 'application/json');
        $this->assertEquals(FeedType::JsonFeed, $feedType);

        $feedType = $detectorFactory->detect($body, 'plain/text');
        $this->assertEquals(FeedType::JsonFeed, $feedType);
    }

    public function testAtomFeedTypeDetection(): void
    {
        if (!file_exists(__DIR__ . self::ATOM_FIXTURE)) {
            $this->fail('Json feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::ATOM_FIXTURE);

        $detectorFactory = new FeedTypeDetector();

        $feedType = $detectorFactory->detect($body, 'application/atom+xml');
        $this->assertEquals(FeedType::Atom, $feedType);

        $feedType = $detectorFactory->detect($body, 'application/json');
        $this->assertEquals(FeedType::Atom, $feedType);
    }

    public function testRssFeedTypeDetection(): void
    {
        if (!file_exists(__DIR__ . self::RSS_FIXTURE)) {
            $this->fail('Json feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::RSS_FIXTURE);

        $detectorFactory = new FeedTypeDetector();

        $feedType = $detectorFactory->detect($body, 'application/rss+xml');
        $this->assertEquals(FeedType::Rss, $feedType);

        $feedType = $detectorFactory->detect($body, 'application/json');
        $this->assertEquals(FeedType::Rss, $feedType);
    }

    public function testJsonFeedParse()
    {
        if (!file_exists(__DIR__ . self::JSON_FIXTURE)) {
            $this->fail('Json feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::JSON_FIXTURE);

        $jsonParser = new FeedJsonParser();
        $result = $jsonParser->parse($body);

        $this->assertEquals('Daring Fireball', $result->title);
        $this->assertCount(48, $result->articles);
    }

    public function testAtomFeedParse()
    {
        if (!file_exists(__DIR__ . self::ATOM_FIXTURE)) {
            $this->fail('Atom feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::ATOM_FIXTURE);

        $jsonParser = new FeedAtomParser();
        $result = $jsonParser->parse($body);

        $this->assertEquals('Release notes from symfony', $result->title);
        $this->assertCount(10, $result->articles);
    }

    public function testRss2FeedParse()
    {
        if (!file_exists(__DIR__ . self::RSS_FIXTURE)) {
            $this->fail('Rss feed file does not exist');
        }

        $body = file_get_contents(__DIR__ . self::RSS_FIXTURE);

        $jsonParser = new FeedRss2Parser();
        $result = $jsonParser->parse($body);

        $this->assertEquals('Hacker News', $result->title);
        $this->assertCount(30, $result->articles);
    }
}
