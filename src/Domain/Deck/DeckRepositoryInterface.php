<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Domain\Deck\Deck AS DeckModel;
use App\Entity\Deck;

interface DeckRepositoryInterface
{
	public function save(DeckModel $deck): Deck;

//	public function getById(string $id): Deck;
}