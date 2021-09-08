<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck\Validator;

use App\Infrastructure\Deck\DeckDTOInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class DeckAddDTO implements DeckDTOInterface
{
	/**
	 * @Assert\Uuid
	 * @Assert\NotBlank(
	 *     message="Card id can't be empty"
	 * )
	 */
	private Uuid $user_id;

	public function __construct(Uuid $user_id)
	{
		$this->user_id = $user_id;
	}

	public function getUserId(): Uuid
	{
		return $this->user_id;
	}
}