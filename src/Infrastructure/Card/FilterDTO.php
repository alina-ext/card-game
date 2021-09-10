<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Infrastructure\DTOInterface;

class FilterDTO implements DTOInterface
{
	private int $page_id;

	public function getPageId(): int
	{
		return $this->page_id;
	}

	public function setPageId(int $page_id): void
	{
		$this->page_id = $page_id;
	}
}