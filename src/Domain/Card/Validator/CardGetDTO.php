<?php
declare(strict_types=1);

namespace App\Domain\Card\Validator;

use App\Infrastructure\Card\CardDTOInterface;
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

	public function __construct(Uuid $id)
	{
		$this->id = $id;
	}
}