<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;

use App\Entity\Event;

class Subscriber
{
	public function notify(Event $event): void
	{
		/*TODO implement*/
	}

	public function unsubscribe(Event $event): void
	{
		/*TODO implement*/
	}

	public function isSubscribed(Event $event): bool
	{
		return true;
	}
}