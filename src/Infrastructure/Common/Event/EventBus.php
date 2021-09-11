<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;

interface EventBus
{
	public function dispatch(Event $event): void;
}