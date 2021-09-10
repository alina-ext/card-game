<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Card\Card AS CardModel;
use App\Infrastructure\Card\FilterService;

interface CardRepositoryInterface
{
	/**
	 * @param CardModel $card
	 */
	public function save(CardModel $card): void;

	/**
	 * @param string $id
	 * @return CardModel
	 */
	public function getById(string $id): CardModel;

	/**
	 * @param string[] $ids
	 * @return CardModel[]
	 */
	public function getByIds(array $ids): array;

	/**
	 * @param FilterService $filter
	 * @return CardCollection
	 */
	public function getList(FilterService $filter): CardCollection;
}