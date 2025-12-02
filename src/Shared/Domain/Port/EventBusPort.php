<?php

namespace App\Shared\Domain\Port;

use App\Shared\Domain\Event\DomainEvent;

interface EventBusPort
{
    public function dispatch(DomainEvent $event): void;
}
