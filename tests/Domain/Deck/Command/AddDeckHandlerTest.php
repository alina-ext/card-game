<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck\Command;

use App\Domain\Deck\Command\AddDeckCommand;
use App\Domain\Deck\Command\AddDeckHandler;
use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckAddDTO;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AddDeckHandlerTest extends TestCase
{
	private DeckRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(DeckRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->eventRepository = $this->createMock(EventRepository::class);
		$this->eventBus = $this->createMock(EventBus::class);
		$this->uuidGenerator = new UuidGenerator();
	}

	public function testHandle()
	{
		$command = new AddDeckCommand($this->createDeckDTO($this->uuidGenerator->generate(), $this->uuidGenerator->generate()));
		$dto = $command->getDto();
		$model = Deck::createDeck(
			$this->uuidGenerator->toString($dto->getId()),
			$this->uuidGenerator->toString($dto->getUserId()),
		);

		$this->validator->expects(self::once())->method('validate')->with($dto);
		$this->repository->expects(self::once())
			->method('save')
			->with($model);

		$handler = new AddDeckHandler(
			$this->repository,
			$this->validator,
			$this->uuidGenerator,
			$this->eventRepository,
			$this->eventBus
		);
		$handler($command);
	}

	/**
	 * @param Uuid $deckId
	 * @param Uuid $userId
	 * @return DeckAddDTO
	 */
	private function createDeckDTO(Uuid $deckId, Uuid $userId): DeckAddDTO
	{
		$dto = new DeckAddDTO();
		$dto->setId($deckId);
		$dto->setUserId($userId);

		return $dto;
	}
}
