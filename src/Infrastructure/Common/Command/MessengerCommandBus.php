<?php

namespace App\Infrastructure\Common\Command;

use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBus implements CommandBus
{
	private MessageBusInterface $commandBus;

	public function __construct(MessageBusInterface $commandBus)
	{
		$this->commandBus = $commandBus;
	}

	public function dispatch(Command $command): void
	{
		$this->commandBus->dispatch($command);
	}
}