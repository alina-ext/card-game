<?php

namespace App\Domain\Card\Command;

use App\Infrastructure\Common\Command\Command;
use Symfony\Component\Uid\Uuid;

class AddCardCommand implements Command
{
	private Uuid $id;
	private string $title;
	private string $power;

	public function __construct(Uuid $id, string $title, string $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getPower(): string
	{
		return $this->power;
	}
}