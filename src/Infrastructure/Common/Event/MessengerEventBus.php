<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;

use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventBus implements EventBus
{
	private MessageBusInterface $eventBus;

	public function __construct(MessageBusInterface $eventBus)
	{
		$this->eventBus = $eventBus;
	}

	/**
	 * @param Event $event
	 */
	public function dispatch(Event $event): void
	{
		$this->eventBus->dispatch($event);
	}
}