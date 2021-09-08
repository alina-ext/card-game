<?php
declare(strict_types=1);

namespace App\Infrastructure;

interface Payload
{
	public function getPayload(): array;
}
