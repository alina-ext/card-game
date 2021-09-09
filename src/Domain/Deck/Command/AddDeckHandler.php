<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\ValidatorInterface;

class AddDeckHandler implements CommandHandler
{
	private DeckRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		DeckRepositoryInterface $repository,
		GeneratorInterface $uuidGenerator
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(AddDeckCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = new Deck(
			$this->uuidGenerator->toString($dto->getId()),
			$this->uuidGenerator->toString($dto->getUserId())
		);
		$model->pushEvent('deck:add');

		$this->repository->save($model);
	}
}