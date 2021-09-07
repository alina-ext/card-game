<?php

namespace App\Infrastructure\Card;

use App\Domain\Card\Exceptions\ValidationException;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardGetDTO implements CardDTOInterface
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
		try {
			$id = Uuid::fromString($id);
		} catch (InvalidArgumentException $e) {
			throw new ValidationException($e->getMessage());
		}
		$this->id = $id;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}
}