<?php
declare(strict_types=1);

namespace App\Domain\Deck\Event;

use App\Infrastructure\Common\Event\Event AS DomainEvent;

class Event implements DomainEvent
{
	public string $title;
	private string $deckId;

	private string $userId;

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

	public function getData(): array {
		return [
			'deckId' => $this->deckId,
			'userId' => $this->userId
		];
	}

	public function setDeckId(string $deckId): void
	{
		$this->deckId = $deckId;
	}

	public function setUserId(string $userId): void
	{
		$this->userId = $userId;
	}

}