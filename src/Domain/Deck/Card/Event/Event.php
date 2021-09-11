<?php
declare(strict_types=1);

namespace App\Domain\Deck\Card\Event;

use App\Infrastructure\Common\Event\Event as DomainEvent;

class Event implements DomainEvent
{
	public string $title;
	private string $deckId;
	private string $cardId;
	private string $cardTitle;
	private int $cardPower;
	private int $cardAmount;

	public function __construct(string $title)
	{
		$this->title = $title;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDeckId(): string
	{
		return $this->deckId;
	}

	public function getCardId(): string
	{
		return $this->cardId;
	}

	public function getCardAmount(): int
	{
		return $this->cardAmount;
	}

	public function getData(): array
	{
		return [
			'deckId' => $this->deckId,
			'cardId' => $this->cardId,
			'cardTitle' => $this->cardTitle,
			'cardPower' => $this->cardPower,
			'cardAmount' => $this->cardAmount
		];
	}

	public function setDeckId(string $deckId): void
	{
		$this->deckId = $deckId;
	}

	public function setCardId(string $cardId): void
	{
		$this->cardId = $cardId;
	}

	public function setCardTitle(string $cardTitle): void
	{
		$this->cardTitle = $cardTitle;
	}

	public function setCardPower(int $cardPower): void
	{
		$this->cardPower = $cardPower;
	}

	public function setCardAmount(int $cardAmount): void
	{
		$this->cardAmount = $cardAmount;
	}
}