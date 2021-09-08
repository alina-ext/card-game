<?php
declare(strict_types=1);

namespace App\Domain\Deck\Validator;

use App\Infrastructure\Deck\DeckDTOInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class DeckAddDTO implements DeckDTOInterface
{
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="Deck id can't be empty"
	 * )
	 */
	private Uuid $id;
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="User_id can't be empty"
	 * )
	 */
	private Uuid $user_id;

	public function __construct(Uuid $id, Uuid $userId)
	{
		$this->id = $id;
		$this->user_id = $userId;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function getUserId(): Uuid
	{
		return $this->user_id;
	}
}