<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Domain\Deck\Deck as DeckModel;
use App\Entity\Deck;

interface DeckRepositoryInterface
{
	public function save(DeckModel $deck): Deck;

	public function saveCard(DeckModel $deck): void;

	public function getById(string $id): DeckModel;
}