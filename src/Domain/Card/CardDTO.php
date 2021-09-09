<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\Card\CardDTOInterface;
use Symfony\Component\Uid\Uuid;

class CardDTO implements CardDTOInterface
{
	private Uuid $id;
	private string $title;
	private int $power;

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function setId(Uuid $id): void
	{
		$this->id = $id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getPower(): int
	{
		return $this->power;
	}

	public function setPower(int $power): void
	{
		$this->power = $power;
	}
}