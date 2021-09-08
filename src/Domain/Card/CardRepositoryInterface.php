<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Card\Card AS CardModel;
use App\Domain\Card\Validator\CardAddDTO;
use App\Entity\Card;

interface CardRepositoryInterface
{
	public function save(CardAddDTO $cardDTO): Card;

	public function getById(string $id): object;

	public function delete(CardModel $card): CardModel;
}