<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Infrastructure\Card\CardDTO;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Card\CardUpdateDTO;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Card\ValidatorInterface;
use Symfony\Component\Uid\Uuid;

class UpdateCardHandler implements CommandHandler
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

	public function __invoke(UpdateCardCommand $command): void
	{
		$id = Uuid::fromString($command->getId());
		$cardDTO = new CardUpdateDTO(
			$id,
			$command->getTitle(),
			$command->getPower()
		);
		$this->validator->validate($cardDTO);

		$modelDB = $this->repository->getById($command->getId());
		if (($title = $cardDTO->getTitle())) {
			$modelDB->setTitle($title);
		}
		$power = $cardDTO->getPower();
		if (null !== $power) {
			$modelDB->setPower(intval($power));
		}
		$cardDTO = new CardDTO(
			$id,
			$modelDB->getTitle(),
			$modelDB->getPower()
		);
		/*$card = */$this->repository->update($cardDTO);
	}
}