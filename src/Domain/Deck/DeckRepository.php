<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Domain\Deck\Deck as DeckModel;

interface DeckRepository
{
	/**
	 * @param DeckModel $deck
	 */
	public function save(DeckModel $deck): void;

	/**
	 * @param DeckModel $deck
	 */
	public function saveAggregateCards(DeckModel $deck): void;

	/**
	 * @param string $id
	 * @return DeckModel
	 */
	public function getById(string $id): DeckModel;
}