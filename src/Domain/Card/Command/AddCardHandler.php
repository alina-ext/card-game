<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Validator\CardAddDTO;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Card\ValidatorInterface;

class AddCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository
	) {
		$this->validator = $validator;
		$this->repository = $repository;
	}
	public function __invoke(AddCardCommand $command): void
	{
		$cardDTO = new CardAddDTO(
			$command->getId(),
			$command->getTitle(),
			$command->getPower()
		);
		$this->validator->validate($cardDTO);
		$this->repository->save($cardDTO);
	}
}