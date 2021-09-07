<?php

namespace App\Domain\Card;

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
}