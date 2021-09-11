<?php
declare(strict_types=1);

namespace App\Infrastructure\Common;

use App\Infrastructure\Common\Event\Event as DomainEvent;

interface EventRepository
{
	/**
	 * @param DomainEvent $event
	 */
	public function save(DomainEvent $event): void;
}