<?php
declare(strict_types=1);

namespace App\Infrastructure\Card\Validator;

use App\Infrastructure\Card\CardDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CardAddDTO implements CardDTOInterface
{
	/**
	 * @Assert\Type(
	 *     "string",
	 *     message="Cart title {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\Length(
	 *     min=2,
	 *     max=255,
	 *     minMessage="Card title minimum length is {{ limit }} characters",
	 *     maxMessage="Card title maximum length is {{ limit }} characters"
	 * )
	 */
	private ?string $title;

	/**
	 * @Assert\NotBlank(
	 *     message="Card power can't be empty"
	 * )
	 * @Assert\Type(
	 *	 "digit",
	 *	 message="Card power {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\PositiveOrZero()
	 * @Assert\LessThan(65535)
	 */
	private ?string $power;

	public function __construct(?string $title, ?string $power)
	{
		$this->title = $title;
		$this->power = $power;
	}
}