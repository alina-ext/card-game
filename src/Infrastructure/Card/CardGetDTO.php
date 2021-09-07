<?php

namespace App\Infrastructure\Card;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardGetDTO
{
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="Card id can't be empty"
	 * )
	 */
	private Uuid $id;

	public function __construct(string $id)
	{
		$this->id = Uuid::fromString($id);
	}

	public function getId(): Uuid
	{
		return $this->id;
	}
}