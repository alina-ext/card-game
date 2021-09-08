<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Card\Card AS CardModel;
use App\Entity\Card;
use App\Infrastructure\Card\CardDTO;

interface CardRepositoryInterface
{
	public function save(CardDTO $cardDTO): Card;

	public function getById(string $id): object;

	public function delete(CardModel $card): CardModel;
}