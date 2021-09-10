<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\DTOInterface;
use Symfony\Component\Uid\Uuid;

class CardEditDTO implements DTOInterface
{
	private Uuid $id;
	private ?string $title;
	private ?int $power;

	public function __construct()
	{
		$this->title = null;
		$this->power = null;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function setId(Uuid $id): void
	{
		$this->id = $id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getPower(): ?int
	{
		return $this->power;
	}

	public function setPower(int $power): void
	{
		$this->power = $power;
	}
}