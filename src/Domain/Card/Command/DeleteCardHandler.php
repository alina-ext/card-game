<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Validator\CardGetDTO;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class DeleteCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

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

		$entity = $this->repository->getById(
			$this->uuidGenerator->toString($command->getId())
		);
		$model = new Card(
			$entity->getId(),
			$entity->getTitle(),
			$entity->getPower(),
			true
		);
		$model->setEntity($entity);
		$this->repository->save($model);
	}
}