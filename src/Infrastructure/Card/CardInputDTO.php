<?php

namespace App\Infrastructure\Card;

use Symfony\Component\Validator\Constraints as Assert;

class CardInputDTO
{
	/**
	 * @Assert\Type("string")
	 */
	private ?string $id;

	/**
	 * @Assert\Type(
	 *     "string",
	 *     message="Cart title {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\Length(
	 *     min=2,
	 *     max=255,
	 *     minMessage="Card title minimum length is 2 characters",
	 *     maxMessage="Card title maximum length is 255 characters"
	 * )
	 */
	private string $title;

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
	private string $power;

	public function __construct(?string $id, string $title, string $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}
}