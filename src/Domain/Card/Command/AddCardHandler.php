<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Validator\CardAddDTO;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Card\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Domain\Card\Card;

class AddCardHandler implements CommandHandler
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

	public function __invoke(AddCardCommand $command): void
	{
		$cardDTO = new CardAddDTO(
			$command->getId(),
			$command->getTitle(),
			$command->getPower()
		);
		$this->validator->validate($cardDTO);
		$model = new Card(
			$this->uuidGenerator->toString($cardDTO->getId()),
			$cardDTO->getTitle(),
			$cardDTO->getPower()
		);
		$model->pushEvent('card:add');

		$this->repository->save($model);
	}
}