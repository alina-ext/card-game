<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\Validator\CardUpdateDTO;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class UpdateCardHandler implements CommandHandler
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

	public function __invoke(UpdateCardCommand $command): void
	{
		$cardDTO = new CardUpdateDTO(
			$command->getId(),
			$command->getTitle(),
			$command->getPower()
		);
		$this->validator->validate($cardDTO);

		$entity = $this->repository->getById(
			$this->uuidGenerator->toString($command->getId())
		);
		$model = new Card(
			$entity->getId(),
			$entity->getTitle(),
			$entity->getPower()
		);
		$model->setEntity($entity);
		$title = $cardDTO->getTitle();
		if (null !== $title) {
			$model->setTitle($title);
			$model->pushEvent('card:update:title');
		}
		$power = $cardDTO->getPower();
		if (null !== $power) {
			$model->setPower(intval($power));
			$model->pushEvent('card:update:power');
		}

		$this->repository->save($model);
	}
}