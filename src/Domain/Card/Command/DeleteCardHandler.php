<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Card\CardGetDTO;
use App\Infrastructure\Card\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class DeleteCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;
	private GeneratorInterface $uuidGenerator;
	private ValidatorInterface $validator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
		GeneratorInterface $uuidGenerator
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}
	public function __invoke(DeleteCardCommand $command): void
	{
		$cardDTO = new CardGetDTO(
			$command->getId(),
		);
		$this->validator->validate($cardDTO);

		$modelDB = $this->repository->getById($this->uuidGenerator->toString($command->getId()));
		$cart = new Card(
			$modelDB->getId(),
			$modelDB->getTitle(),
			$modelDB->getPower(),
			true
		);
		$this->repository->delete($cart);
	}
}