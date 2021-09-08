<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\Payload;

class Response implements Payload
{
	private string $id;
	private string $title;
	private int $power;

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): void
	{
		$this->id = $id;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function setPower(int $power): void
	{
		$this->power = $power;
	}

	public function getPayload(): array
	{
		return ['id' => $this->id, 'title' => $this->title, 'power' => $this->power];
	}
}
