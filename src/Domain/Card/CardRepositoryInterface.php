<?php

namespace App\Domain\Card;

use App\Infrastructure\Card\CardDTO;

interface CardRepositoryInterface
{
	public function save(CardDTO $cardDTO): void;
}