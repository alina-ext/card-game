<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Infrastructure\Common\Command\Command;
use Symfony\Component\Uid\Uuid;

class AddDeckCommand implements Command
{
	private Uuid $id;
	private Uuid $userId;

	public function __construct(Uuid $id, Uuid $userId)
	{
		$this->id = $id;
		$this->userId = $userId;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function getUserId(): Uuid
	{
		return $this->userId;
	}
}