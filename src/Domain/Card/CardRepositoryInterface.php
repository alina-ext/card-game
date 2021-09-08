<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Card\Card AS CardModel;
use App\Entity\Card;

interface CardRepositoryInterface
{
	public function save(CardModel $card): Card;

	public function getById(string $id): Card;
}