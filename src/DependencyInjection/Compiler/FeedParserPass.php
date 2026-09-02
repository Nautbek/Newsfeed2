<?php

namespace App\DependencyInjection\Compiler;

use App\Feed\Parser\FeedParserInterface;
use App\Feed\Parser\FeedParserRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FeedParserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FeedParserRegistry::class)) {
            return;
        }

        $chosen = [];

        foreach ($container->findTaggedServiceIds('app.feed_parser') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();

            /** @var class-string<FeedParserInterface> $class */
            $type = $class::getSupportedType();

            $priority = 0;
            foreach ($tags as $attributes) {
                $priority = max($priority, $attributes['priority'] ?? 0);
            }

            if (!isset($chosen[$type]) || $priority > $chosen[$type]['priority']) {
                $chosen[$type] = ['ref' => new Reference($id), 'priority' => $priority];
            }
        }

        $container->getDefinition(FeedParserRegistry::class)
            ->setArgument('$parsers', array_map(
                static fn (array $c): Reference => $c['ref'],
                $chosen,
            ));
    }
}
