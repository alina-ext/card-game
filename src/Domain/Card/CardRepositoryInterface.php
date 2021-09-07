<?php

namespace App\Domain\Card;

use App\Infrastructure\Card\CardDTO;
use App\Entity\Card;

interface CardRepositoryInterface
{
	public function save(CardDTO $cardDTO): Card;

	public function getById(string $id): Card;
}