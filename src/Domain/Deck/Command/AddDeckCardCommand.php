<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\Card\DeckCardDTO;
use App\Infrastructure\Common\Command\Command;

class AddDeckCardCommand implements Command
{
	private DeckCardDTO $dto;

	public function __construct(DeckCardDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): DeckCardDTO
	{
		return $this->dto;
	}
}