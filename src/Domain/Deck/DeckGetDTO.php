<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Infrastructure\Deck\DeckDTOInterface;
use Symfony\Component\Uid\Uuid;

class DeckGetDTO implements DeckDTOInterface
{
	private Uuid $id;

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function setId(Uuid $id): void
	{
		$this->id = $id;
	}
}