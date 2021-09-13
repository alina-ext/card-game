<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\ValidatorInterface;

class AddDeckHandler implements CommandHandler
{
	private DeckRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function __construct(
		DeckRepository $repository,
		ValidatorInterface $validator,
		GeneratorInterface $uuidGenerator,
		EventRepository $eventRepository,
		EventBus $eventBus)
	{
		$this->repository = $repository;
		$this->validator = $validator;
		$this->uuidGenerator = $uuidGenerator;
		$this->eventRepository = $eventRepository;
		$this->eventBus = $eventBus;
	}

	public function __invoke(AddDeckCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = Deck::createDeck(
			$this->uuidGenerator->toString($dto->getId()),
			$this->uuidGenerator->toString($dto->getUserId()),
		);

		$this->repository->save($model);
		$model->dispatch($this->eventRepository, $this->eventBus);
	}
}