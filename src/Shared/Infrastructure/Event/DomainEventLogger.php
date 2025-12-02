<?php

namespace App\Shared\Infrastructure\Event;

use App\Shared\Domain\Event\DomainEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: DomainEvent::class)]
final readonly class DomainEventLogger
{
    public function __construct(
        private LoggerInterface $logger
    )
    {

    }

    public function __invoke(DomainEvent $event): void
    {
        $this->logger->info((new \ReflectionClass($event))->getShortName(), [
            'event_class' => $event::class,
            'occurred_at' => $event->occurredAt(),
            'payload' => $this->normalizeEvent($event),
        ]);
    }

    private function normalizeEvent(object $event): array
    {
        $r = new \ReflectionClass($event);
        $props = [];

        foreach ($r->getProperties() as $property) {
            $property->setAccessible(true);
            $props[$property->getName()] = $property->getValue($event);
        }

        return $props;
    }
}
