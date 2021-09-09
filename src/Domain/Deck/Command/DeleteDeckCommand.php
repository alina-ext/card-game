<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\DeckIdDTO;
use App\Infrastructure\Common\Command\Command;

class DeleteDeckCommand implements Command
{
	private DeckIdDTO $dto;

	public function __construct(DeckIdDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): DeckIdDTO
	{
		return $this->dto;
	}
}