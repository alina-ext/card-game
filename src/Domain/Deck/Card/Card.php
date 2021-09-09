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

	public function __construct(string $id, string $title, int $power, int $amount, bool $deleted = false)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->amount = $amount;
		$this->deleted = $deleted;
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

		$response->setOriginalTitle('');
		$response->setOriginalPower(0);
	}
}