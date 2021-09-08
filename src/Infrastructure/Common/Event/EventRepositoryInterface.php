<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;

use App\Entity\Event;

interface EventRepositoryInterface
{
	public function push(Event $event): void;
}