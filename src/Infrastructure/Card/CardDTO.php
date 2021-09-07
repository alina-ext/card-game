<?php

namespace App\Infrastructure\Card;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardDTO
{
	/**
	 * @Assert\Type("uuid")
	 * @Assert\NotBlank(
	 *     message="Card id can't be empty"
	 * )
	 */
	private Uuid $id;
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

	public function __construct(Uuid $id, string $title, string $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getPower(): int
	{
		return $this->power;
	}
}