<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\Exceptions\ValidationException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardUpdateInputDTO implements CardDTOInterface
{
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="Card id can't be empty"
	 * )
	 */
	private Uuid $id;

	/**
	 * @Assert\Length(
	 *     min=2,
	 *     max=255,
	 *     minMessage="Card title minimum length is {{ limit }} characters",
	 *     maxMessage="Card title maximum length is {{ limit }} characters"
	 * )
	 */
	private ?string $title;

	/**
	 * @Assert\Type(
	 *	 "digit",
	 *	 message="Card power {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\PositiveOrZero()
	 * @Assert\LessThan(65535)
	 */
	private ?string $power;

	public function __construct(Uuid $id, ?string $title, ?string $power)
	{
		if (null === $title && null === $power) {
			throw new ValidationException('Required title or power value');
		}
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}
}