<?php

namespace App\Listener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(event: Events::postPersist)]
class GlobalListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function postPersist(PostPersistEventArgs $eventArgs): void
    {
        $this->logger->info(__CLASS__ . __METHOD__);
    }
}
