<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Card\CardRepository;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\ValidatorInterface;

class DeleteDeckCardHandler implements CommandHandler
{
	private DeckRepository $repository;
	private CardRepository $cardRepository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function __construct(
		DeckRepository $repository,
		CardRepository $cardRepository,
		ValidatorInterface $validator,
		GeneratorInterface $uuidGenerator,
		EventRepository $eventRepository,
		EventBus $eventBus)
	{
		$this->repository = $repository;
		$this->cardRepository = $cardRepository;
		$this->validator = $validator;
		$this->uuidGenerator = $uuidGenerator;
		$this->eventRepository = $eventRepository;
		$this->eventBus = $eventBus;
	}

	public function __invoke(DeleteDeckCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$deckModel = $this->repository->getById($this->uuidGenerator->toString($dto->getDeckId()));

		$deckModel->deleteCards($this->uuidGenerator->toString($dto->getCardId()), $dto->getAmount());

		$this->repository->saveCard($deckModel);
		$deckModel->dispatch($this->eventRepository, $this->eventBus);
	}
}