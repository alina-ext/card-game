<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Infrastructure\Deck\DeckDTOInterface;
use Symfony\Component\Uid\Uuid;

class DeckAddDTO implements DeckDTOInterface
{
	private Uuid $id;
	private Uuid $user_id;

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function setId(Uuid $id): void
	{
		$this->id = $id;
	}

	public function getUserId(): Uuid
	{
		return $this->user_id;
	}

	public function setUserId(Uuid $userId): void
	{
		$this->user_id = $userId;
	}
}