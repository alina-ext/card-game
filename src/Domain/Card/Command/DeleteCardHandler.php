<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
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
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = new Card(
			$this->uuidGenerator->toString($dto->getId()),
			'',
			0,
			true
		);
		$model->pushEvent('card:delete');

		$this->repository->save($model);
	}
}