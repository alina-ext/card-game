<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Deck\ValidatorInterface;

class DeleteDeckHandler implements CommandHandler
{
	private DeckRepositoryInterface $repository;
	private CardRepositoryInterface $cardRepository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		DeckRepositoryInterface $repository,
		CardRepositoryInterface $cardRepository,
		GeneratorInterface $uuidGenerator
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->cardRepository = $cardRepository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(DeleteDeckCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$deckModel = $this->repository->getById($this->uuidGenerator->toString($dto->getId()));
		$deckModel->setDeleted();
		$deckModel->pushEvent('deck:delete');

		$this->repository->save($deckModel);

//		$response = new Response();
//		$deckModel->fillResponse($response);
	}
}