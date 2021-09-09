<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Infrastructure\Payload;

class Response implements Payload
{
	private string $id;
	private string $userId;
	private array $cards;
	private int $power;

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): void
	{
		$this->id = $id;
	}

	public function setUserId(string $userId): void
	{
		$this->userId = $userId;
	}

	public function setCards(array $cards): void
	{
		$this->cards = $cards;
	}

	public function setPower(int $power): void
	{
		$this->power = $power;
	}

	public function getPayload(): array
	{
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'cards' => $this->cards,
			'power' => $this->power,
		];
	}
}
