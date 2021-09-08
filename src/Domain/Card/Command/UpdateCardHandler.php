<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\Validator\CardAddDTO;
use App\Domain\Card\Validator\CardUpdateDTO;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Card\ValidatorInterface;
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

		$modelDB = $this->repository->getById($this->uuidGenerator->toString($command->getId()));
		if (($title = $cardDTO->getTitle())) {
			$modelDB->setTitle($title);
		}
		$power = $cardDTO->getPower();
		if (null !== $power) {
			$modelDB->setPower(intval($power));
		}
		$cardDTO = new CardAddDTO(
			$command->getId(),
			$modelDB->getTitle(),
			$modelDB->getPower()
		);
		$this->repository->update($cardDTO);
	}
}