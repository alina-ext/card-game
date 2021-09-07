<?php

namespace App\Infrastructure\Common\Command;

interface CommandBus
{
	public function dispatch(Command $command): void;
}