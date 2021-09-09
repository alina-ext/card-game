<?php
declare(strict_types=1);

namespace App\Domain\Card\Validator;

use App\Infrastructure\Card\CardDTOInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CardAddDTO implements CardDTOInterface
{
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="Card id can't be empty"
	 * )
	 */
	private Uuid $id;
	/**
	 * @Assert\Type(
	 *     "string",
	 *     message="Card title {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\Length(
	 *     min=2,
	 *     max=255,
	 *     minMessage="Card title minimum length is {{ limit }} characters",
	 *     maxMessage="Card title maximum length is {{ limit }} characters"
	 * )
	 */
	private string $title;

	/**
	 * @Assert\NotBlank(
	 *     message="Card power can't be empty"
	 * )
	 * @Assert\Type(
	 *	 "int",
	 *	 message="Card power {{ value }} is not a valid {{ type }}"
	 * )
	 * @Assert\PositiveOrZero()
	 * @Assert\LessThan(65535)
	 */
	private int $power;

	public function __construct(Uuid $id, string $title, string|int $power)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = intval($power);
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