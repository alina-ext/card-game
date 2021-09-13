<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\Payload;

class ResponseCatalog implements Payload
{
	private array $items = [];
	private ?string $prev = null;
	private ?string $next = null;

	/**
	 * @param Card[] $items
	 */
	public function setList(array $items): void
	{
		$this->items = $items;
	}

	public function setPrev(?string $prev): void
	{
		$this->prev = $prev;
	}

	public function setNext(?string $next): void
	{
		$this->next = $next;
	}

	public function getPayload(): array
	{
		$payload = [
			'items' => $this->items,
			'_links' => [],
		];

		if ($this->prev) {
			$payload['_links']['prev'] = $this->prev;
		}
		if ($this->next) {
			$payload['_links']['next'] = $this->next;
		}

		return $payload;
	}
}