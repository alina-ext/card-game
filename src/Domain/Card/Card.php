<?php
declare(strict_types=1);

namespace App\Domain\Card;

class Card
{
	private string $id;
	private string $title;
	private int $power;
	private bool $deleted;

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

	public function getDeleted(): int
	{
		return $this->power;
	}

	public function __construct(string $id, string $title, int $power, bool $deleted = false)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->deleted = $deleted;
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setTitle($this->title);
		$response->setPower($this->power);
	}
}