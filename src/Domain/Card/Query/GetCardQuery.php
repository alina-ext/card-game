<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\CardIdDTO;
use App\Infrastructure\Common\Query\Query;

class GetCardQuery implements Query
{
	private CardIdDTO $dto;

	public function __construct(CardIdDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): CardIdDTO
	{
		return $this->dto;
	}
}