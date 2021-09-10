<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Infrastructure\Card\FilterDTO;
use App\Infrastructure\Common\Query\Query;

class GetCardListQuery implements Query
{
	private FilterDTO $dto;

	public function __construct(FilterDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): FilterDTO
	{
		return $this->dto;
	}
}