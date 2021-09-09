<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\DeckAddDTO;
use App\Infrastructure\Common\Command\Command;

class AddDeckCommand implements Command
{
	private DeckAddDTO $dto;

	public function __construct(DeckAddDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): DeckAddDTO
	{
		return $this->dto;
	}
}