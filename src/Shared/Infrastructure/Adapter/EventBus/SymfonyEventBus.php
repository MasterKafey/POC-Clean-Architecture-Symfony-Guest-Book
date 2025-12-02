<?php

namespace App\Shared\Infrastructure\Adapter\EventBus;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Port\EventBusPort;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyEventBus implements EventBusPort
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    )
    {

    }

    public function dispatch(DomainEvent $event): void
    {
        $this->dispatcher->dispatch($event, $event::class);
        $this->dispatcher->dispatch($event, DomainEvent::class);
    }
}
