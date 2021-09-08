<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Infrastructure\Common\Command\Command;

class DeleteCardCommand implements Command
{
	private string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public function getId(): string
	{
		return $this->id;
	}
}