<?php
declare(strict_types=1);

namespace App\Domain\Deck\Card;

use App\Infrastructure\Deck\DeckDTOInterface;
use Symfony\Component\Uid\Uuid;

class DeckCardDTO implements DeckDTOInterface
{
	private Uuid $deck_id;
	private Uuid $card_id;
	private int $amount;

	public function getDeckId(): Uuid
	{
		return $this->deck_id;
	}

	public function setDeckId(Uuid $deckId): void
	{
		$this->deck_id = $deckId;
	}

	public function getCardId(): Uuid
	{
		return $this->card_id;
	}

	public function setCardId(Uuid $cardId): void
	{
		$this->card_id = $cardId;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function setAmount(int $amount): void
	{
		$this->amount = $amount;
	}
}