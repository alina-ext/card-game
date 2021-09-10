<?php
declare(strict_types=1);

namespace App\Domain\Deck\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\ValidatorInterface;

class DeleteDeckCardHandler implements CommandHandler
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

	public function __invoke(DeleteDeckCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$deckModel = $this->repository->getById($this->uuidGenerator->toString($dto->getDeckId()));

		$deckModel->deleteCards($this->uuidGenerator->toString($dto->getCardId()), $dto->getAmount());
		$deckModel->pushEvent('deck:card:delete');

		$this->repository->saveCard($deckModel);
	}

	public static function getHandledMessages(): iterable
	{
		yield DeleteDeckCardCommand::class => [
			'method' => '__invoke'
		];
	}
}