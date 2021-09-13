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

	private function __construct(string $id, string $title, int $power, int $amount)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->amount = $amount;
		$this->deleted = false;
		$this->originalTitle = null;
		$this->originalPower = null;
	}

	public static function createCard(string $id, string $title, int $power, int $amount) {
		return new self($id, $title, $power, $amount);
	}

	public function setOriginalData(bool $deleted, ?string $title = null, ?int $power = null) {
		$this->deleted = $deleted;
		if (null !== $title) {
			$this->originalTitle = $title;
		}
		if (null !== $power) {
			$this->originalPower = $power;
		}
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