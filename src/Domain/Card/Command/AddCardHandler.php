<?php

namespace App\Domain\Card\Command;

use App\Infrastructure\Card\CardDTO;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
	) {
		$this->validator = $validator;
		$this->repository = $repository;
	}

	public function __invoke(AddCardCommand $command): void
	{
		$cardDTO = new CardDTO($command->getId(),
			$command->getTitle(),
			$command->getPower()
		);
		$this->validator->validate($cardDTO);
		$this->repository->save($cardDTO);

//		$this->eventBus->handle(new AddCardEvent($cardDTO->getId()));
	}
}