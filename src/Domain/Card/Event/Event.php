<?php
declare(strict_types=1);

namespace App\Domain\Card\Event;

use App\Infrastructure\Common\Event\Event AS DomainEvent;

class Event implements DomainEvent
{
	public string $title;
	private string $cardId;
	private string $cardTitle;
	private int $cardPower;

	public function __construct(string $title)
	{
		$this->title = $title;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getCardId(): string
	{
		return $this->cardId;
	}

	public function getData(): array {
		return [
			'cardId' => $this->cardId,
			'cardTitle' => $this->cardTitle,
			'cardPower' => $this->cardPower
		];
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
}