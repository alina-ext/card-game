<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Infrastructure\Payload;

class Response implements Payload
{
	private string $id;
	private string $userId;

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

	public function getPayload(): array
	{
		return ['id' => $this->id, 'user_id' => $this->userId];
	}
}
