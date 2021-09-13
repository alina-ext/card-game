<?php
declare(strict_types=1);

namespace App\Domain\Deck\Card;

use App\Infrastructure\Payload;

class Response implements Payload
{
	private string $id;
	private string $title;
	private int $power;
	private int $amount;
	private bool $isDeleted;
	private ?string $originalTitle;
	private ?int $originalPower;

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

	public function setAmount(int $amount): void
	{
		$this->amount = $amount;
	}

	public function setOriginalTitle(?string $title): void
	{
		$this->originalTitle = $title;
	}

	public function setOriginalPower(?int $power): void
	{
		$this->originalPower = $power;
	}

	public function setIsDeleted(bool $isDeleted): void
	{
		$this->isDeleted = $isDeleted;
	}

	public function getPayload(): array
	{
		$payload = [
			'id' => $this->id,
			'title' => $this->title,
			'power' => $this->power,
			'amount' => $this->amount,
			'changes' => [],
		];
		if ($this->isDeleted) {
			$payload['is_deleted'] = $this->isDeleted;
		}
		if ($this->originalTitle && strcmp($this->originalTitle, $this->title) !== 0) {
			$payload['changes']['title'] = $this->originalTitle;
		}
		if ($this->originalPower && $this->originalPower != $this->power) {
			$payload['changes']['power'] = $this->originalPower;
		}
		if (!$payload['changes']) {
			unset($payload['changes']);
		}

		return $payload;
	}
}