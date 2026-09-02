<?php

namespace App\Tests;

use App\DependencyInjection\Compiler\FeedParserPass;
use App\Feed\Parser\FeedParserRegistry;
use App\Feed\Parser\FeedRss2Parser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPassTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $c = new ContainerBuilder();
        $c->register(FeedParserRegistry::class)->setArgument('$parsers', []);
        $c->register('parser.rss.old', FeedRss2Parser::class)->addTag('app.feed_parser');
        $c->register('parser.rss.new', FeedRss2Parser::class)->addTag('app.feed_parser', ['priority' => 10]);

        (new FeedParserPass())->process($c);

        $args = $c->getDefinition(FeedParserRegistry::class)->getArgument('$parsers');
        $this->assertSame('parser.rss.new', (string) $args['rss']);
    }
}
