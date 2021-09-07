<?php

namespace App\Domain\Card\Query;

use App\Infrastructure\Common\Query\Query;

class GetCardQuery implements Query
{
	private string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public function getId(): string
	{
		return $this->id;
	}
}