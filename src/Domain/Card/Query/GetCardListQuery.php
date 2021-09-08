<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Infrastructure\Common\Query\Query;

class GetCardListQuery implements Query
{
	private int $page;

	public function __construct(int $page)
	{
		$this->page = $page;
	}

	public function getPage(): int
	{
		return $this->page;
	}
}