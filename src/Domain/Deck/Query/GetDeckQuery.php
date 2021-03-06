<?php
declare(strict_types=1);

namespace App\Domain\Deck\Query;

use App\Domain\Deck\DeckIdDTO;
use App\Infrastructure\Common\Query\Query;

class GetDeckQuery implements Query
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