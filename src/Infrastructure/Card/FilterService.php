<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

class FilterService
{
	private const LIMIT = 3;
	private int $limit;
	private int $offset;
	private int $page;

	public function __construct(int $page, int $limit = self::LIMIT)
	{
		if ($limit < 0) {
			$limit = self::LIMIT;
		}
		$this->limit = $limit;

		if ($page < 1) {
			$page = 1;
		}
		$this->page = $page;

		$this->offset = ($page - 1) * $limit;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}

	public function getOffset(): int
	{
		return $this->offset;
	}

	public function getPage(): int
	{
		return $this->page;
	}
}
