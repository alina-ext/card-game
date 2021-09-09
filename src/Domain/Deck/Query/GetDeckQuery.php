<?php
declare(strict_types=1);

namespace App\Domain\Deck\Query;

use App\Domain\Deck\DeckGetDTO;
use App\Infrastructure\Common\Query\Query;

class GetDeckQuery implements Query
{
	private DeckGetDTO $dto;

	public function __construct(DeckGetDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): DeckGetDTO
	{
		return $this->dto;
	}
}