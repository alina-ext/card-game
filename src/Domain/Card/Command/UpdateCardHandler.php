<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardRepository;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class UpdateCardHandler implements CommandHandler
{
	private CardRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function __construct(
		CardRepository $repository,
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

	public function __invoke(UpdateCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = $this->repository->getById(
			$this->uuidGenerator->toString($dto->getId())
		);
		$model->update($dto);

		$this->repository->save($model);
		$model->dispatch($this->eventRepository, $this->eventBus);
	}

	public static function getHandledMessages(): iterable
	{
		yield UpdateCardCommand::class => [
			'method' => '__invoke'
		];
	}
}