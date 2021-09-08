<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;

class DeleteCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;

	public function __construct(
		CardRepositoryInterface $repository,
	) {
		$this->repository = $repository;
	}
	public function __invoke(DeleteCardCommand $command): void
	{
		$modelDB = $this->repository->getById($command->getId());
		$cart = new Card(
			$modelDB->getId(),
			$modelDB->getTitle(),
			$modelDB->getPower(),
			true
		);
		$this->repository->delete($cart);
	}
}