<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\DTOInterface;
use Symfony\Component\Uid\Uuid;

class CardIdDTO implements DTOInterface
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