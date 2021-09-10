<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Deck\Card\Card;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\ValidatorInterface;

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
	}

	public static function getHandledMessages(): iterable
	{
		yield AddDeckCardCommand::class => [
			'method' => '__invoke'
		];
	}
}