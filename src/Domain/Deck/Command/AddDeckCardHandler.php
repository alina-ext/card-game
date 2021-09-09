<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Deck\Card\Card;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Deck\ValidatorInterface;

class AddDeckCardHandler implements CommandHandler
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

	//deck_id = 6dbd764c-8d7d-4f25-9da5-560c67ae4009
	//user_id = 7b2048d9-1452-4f0e-a4c7-0438a3bc9b67
	//card_id = 4fdec374-833d-485c-a142-eeeb30d733b1
	public function __invoke(AddDeckCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$deckModel = $this->repository->getById($this->uuidGenerator->toString($dto->getDeckId()));

		$cardModel = $this->cardRepository->getById($this->uuidGenerator->toString($dto->getCardId()));
		$cardDto = new Card($cardModel->getId(), $cardModel->getTitle(), $cardModel->getPower(), $dto->getAmount());

		$deckModel->addCard($cardDto);
		$deckModel->pushEvent('deck:card:add');

		$this->repository->saveCard($deckModel);

//		$response = new Response();
//		$deckModel->fillResponse($response);
	}
}