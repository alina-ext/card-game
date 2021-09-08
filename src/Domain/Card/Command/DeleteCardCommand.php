<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Infrastructure\Common\Command\Command;
use Symfony\Component\Uid\Uuid;

class DeleteCardCommand implements Command
{
	private Uuid $id;

	public function __construct(Uuid $id)
	{
		$this->id = $id;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}
}