<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Infrastructure\Common\Command\Command;

class UpdateCardCommand implements Command
{
	private string $id;
	private ?string $title;
	private ?string $power;

	public function __construct(string $id, ?string $title, ?string $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function getPower(): ?string
	{
		return $this->power;
	}
}