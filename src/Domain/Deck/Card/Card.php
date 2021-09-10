<?php
declare(strict_types=1);

namespace App\Domain\Deck\Card;

class Card
{
	private string $id;
	private string $title;
	private int $power;
	private int $amount;
	private bool $deleted;
	private ?string $originalTitle;
	private ?int $originalPower;

	public function __construct(string $id, string $title, int $power, int $amount, bool $deleted = false, string $originalTitle = null, int $originalPower = null)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->amount = $amount;
		$this->deleted = $deleted;
		$this->originalTitle = $originalTitle;
		$this->originalPower = $originalPower;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getPower(): int
	{
		return $this->power;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function setAmount(int $amount): void
	{
		$this->amount = $amount;
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setTitle($this->title);
		$response->setPower($this->power);
		$response->setAmount($this->amount);
		$response->setIsDeleted($this->deleted);
		$response->setOriginalTitle($this->originalTitle);
		$response->setOriginalPower($this->originalPower);
	}
}