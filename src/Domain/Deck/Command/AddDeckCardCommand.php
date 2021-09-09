<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\DeckCardAddDTO;
use App\Infrastructure\Common\Command\Command;

class AddDeckCardCommand implements Command
{
	private DeckCardAddDTO $dto;

	public function __construct(DeckCardAddDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): DeckCardAddDTO
	{
		return $this->dto;
	}
}