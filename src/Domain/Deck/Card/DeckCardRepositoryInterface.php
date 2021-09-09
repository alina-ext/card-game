<?php
declare(strict_types=1);

namespace App\Domain\Deck\Card;

use App\Domain\Deck\Card\Card;

interface DeckCardRepositoryInterface
{
	/**
	 * @param string $deckId
	 * @return Card[]
	 */
	public function getByDeckId(string $deckId): array;

	/**
	 * @param string $deckId
	 * @param Card[] $cards
	 */
	public function addToDeck(string $deckId, array $cards): void;

	/**
	 * @param string $deckId
	 * @param Card[] $cards
	 */
	public function deleteInDeck(string $deckId, array $cards): void;

	/**
	 * @param string $deckId
	 * @param Card[] $cards
	 */
	public function updateInDeck(string $deckId, array $cards): void;
}