<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\Exceptions\ValidationException;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardGetInputDTO implements CardDTOInterface
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
			$this->id = Uuid::fromString($id);
		} catch (InvalidArgumentException $e) {
			throw new ValidationException($e->getMessage());
		}
	}

	public function getId(): Uuid
	{
		return $this->id;
	}
}