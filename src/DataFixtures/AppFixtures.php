<?php

namespace App\DataFixtures;

use App\Factory\FeedFactory;
use App\Factory\SourceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\Persistence\flush_after;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        flush_after(function (): void {
            $source1 = SourceFactory::createOne(
                [
                    'name' => 'Hacker News',
                    'homepageUrl' => 'https://news.ycombinator.com',
                ]
            );
            FeedFactory::createOne(
                [
                    'url' => 'https://news.ycombinator.com/rss',
                    'source' => $source1,
                    'type' => 'rss',
                ]
            );

            $source2 = SourceFactory::createOne(
                [
                    'name' => 'BBC News',
                    'homepageUrl' => 'https://www.bbc.com/news',
                ]
            );
            FeedFactory::createOne(
                [
                    'url' => 'https://feeds.bbci.co.uk/news/rss.xml',
                    'source' => $source2,
                    'type' => 'rss',
                ]
            );
            FeedFactory::createOne(
                [
                    'url' => 'https://feeds.bbci.co.uk/news/technology/rss.xml',
                    'source' => $source2,
                    'type' => 'rss',
                ]
            );
        });
    }
}
