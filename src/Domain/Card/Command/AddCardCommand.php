<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardDTO;
use App\Infrastructure\Common\Command\Command;

class AddCardCommand implements Command
{
	private CardDTO $dto;

	public function __construct(CardDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): CardDTO
	{
		return $this->dto;
	}
}