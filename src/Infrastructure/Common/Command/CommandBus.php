<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Command;

interface CommandBus
{
	public function dispatch(Command $command): void;
}