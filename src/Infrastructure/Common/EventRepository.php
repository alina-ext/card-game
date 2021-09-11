<?php
declare(strict_types=1);

namespace App\Infrastructure\Common;

use App\Infrastructure\Common\Event\Event AS DomainEvent;

interface EventRepository
{
	public function save(DomainEvent $event): void;
}