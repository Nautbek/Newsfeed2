<?php

namespace App\Listener;

use App\Event\ArticleImported;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class ArticleImportedListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {

    }

    #[AsEventListener(event: ArticleImported::class, priority: -1)]
    public function firstArticleImported(ArticleImported $articleImported): void
    {
        $this->logger->info(__CLASS__ . __METHOD__ . ': ' . $articleImported->article->getId());
    }

    #[AsEventListener(event: ArticleImported::class)]
    public function secondArticleImported(ArticleImported $articleImported): void
    {
        $this->logger->info(__CLASS__ . __METHOD__ . ': ' . $articleImported->article->getId());
    }
}
