<?php
declare(strict_types=1);

namespace App\Domain\Card;

class CardCollection
{
	private int $total;
	private array $items = [];

	/**
	 * @param Card[] $items
	 * @param int $total
	 */
	public function __construct(array $items, int $total)
	{
		foreach ($items as $item) {
			$this->items[] = new Card($item->getId(), $item->getTitle(), $item->getPower());
		}
		$this->total = $total;
	}

	public function getTotal(): int
	{
		return $this->total;
	}

	/**
	 * @return Card[]
	 */
	public function getItems(): array
	{
		$data = [];
		foreach ($this->items as $model) {
			$data[] = $model->getCard();
		}

		return $data;
	}
}