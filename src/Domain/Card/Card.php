<?php

namespace App\Domain\Card;

use App\Domain\Card\Response;

class Card
{
	private string $id;
	private string $title;
	private int $power;

	public function __construct(string $id, string $title, int $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setTitle($this->title);
		$response->setPower($this->power);
	}
}