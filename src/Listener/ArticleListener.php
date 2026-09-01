<?php

namespace App\Listener;

use App\Entity\Article;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Psr\Log\LoggerInterface;

readonly class ArticleListener
{

    public function __construct(private LoggerInterface $logger)
    {

    }

    public function postPersist(Article $article, PostPersistEventArgs $args): void
    {
        $this->logger->info(__CLASS__ . __METHOD__ . ': ' . $article->getId());
    }
}
