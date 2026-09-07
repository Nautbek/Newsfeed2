<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestTimingListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {

    }

    #[AsEventListener(event: KernelEvents::REQUEST, priority: 105)]
    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->logger->info('kernel.request', [
            'path' => $event->getRequest()->getPathInfo(),
        ]);
    }
}
