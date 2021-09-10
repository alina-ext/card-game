<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardIdDTO;
use App\Infrastructure\Common\Command\Command;

class DeleteCardCommand implements Command
{
	private CardIdDTO $dto;

	public function __construct(CardIdDTO $dto)
	{
		$this->dto = $dto;
	}

	public function getDto(): CardIdDTO
	{
		return $this->dto;
	}
}