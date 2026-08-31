<?php

namespace App\Tests;

use App\Command\FeedPollCommand;
use App\Factory\ArticleFactory;
use App\Factory\FeedFactory;
use App\Feed\Parser\FeedJsonParser;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ArticleRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function testFindExistingGuids(): void
    {
        self::bootKernel();

        $feed = FeedFactory::createOne();

        $feed2 = FeedFactory::createOne();

        $guids = [
            'guid-test-1',
            'guid-test-2',
        ];

        ArticleFactory::createOne([
            'feed' => $feed,
            'guid' => $guids[0],
        ]);
         ArticleFactory::createOne([
            'feed' => $feed,
            'guid' => $guids[1],
        ]);

        ArticleFactory::createOne([
            'feed' => $feed2,
            'guid' => $guids[1],
        ]);

        $container = static::getContainer();

        /** @var ArticleRepository $ar */
        $ar = $container->get(ArticleRepository::class);
        $res = $ar->findExistingGuids($feed, [...$guids, ...['guid-3']]);

        $this->assertEqualsCanonicalizing($guids, $res);
    }

    public function testHandleFeedIsIdempotent()
    {
        if (!file_exists(__DIR__ . '/fixtures/feeds/daringfireball.json')) {
            $this->fail('Json feed file does not exist');
        }

        $feed = FeedFactory::createOne();

        $body = file_get_contents(__DIR__ . '/fixtures/feeds/daringfireball.json');

        /** @var FeedPollCommand $command */
        $command = self::getContainer()->get(FeedPollCommand::class);

        $jsonParser = new FeedJsonParser();
        $res = $jsonParser->parse($body);

        list($c, $s, $ng) = $command->handleFeed($res, $feed);

        $this->assertEquals(48, $c);
        $this->assertEquals(0, $s);
        $this->assertEquals(0, $ng);

        list($c, $s, $ng) = $command->handleFeed($res, $feed);

        $this->assertEquals(0, $c);
        $this->assertEquals(48, $s);
        $this->assertEquals(0, $ng);
    }
}
