<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardEditDTO;
use App\Infrastructure\Common\Command\Command;

class UpdateCardCommand implements Command
{
	private CardEditDTO $dto;

	public function __construct(CardEditDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): CardEditDTO
	{
		return $this->dto;
	}
}