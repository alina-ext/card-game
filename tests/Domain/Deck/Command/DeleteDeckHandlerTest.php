<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck\Command;

use App\Domain\Deck\Command\DeleteDeckCommand;
use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckIdDTO;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Deck\Command\DeleteDeckHandler;

class DeleteDeckHandlerTest extends TestCase
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
		$this->uuidGenerator = new UuidGenerator();
		$this->eventRepository = $this->createMock(EventRepository::class);
		$this->eventBus = $this->createMock(EventBus::class);
	}


	public function testHandle()
	{
		$command = new DeleteDeckCommand($this->createDeckDTO($this->uuidGenerator->generate()));
		$dto = $command->getDto();
		$this->validator->expects(self::once())->method('validate')->with($dto);

		$userId = $this->uuidGenerator->toString($this->uuidGenerator->generate());
		$model = Deck::createDeck(
			$this->uuidGenerator->toString($dto->getId()),
			$userId
		);

		$this->repository->expects(self::once())
			->method('getById')
			->with($this->uuidGenerator->toString($dto->getId()))
			->willReturn($model);

		$handler = new DeleteDeckHandler(
			$this->repository,
			$this->validator,
			$this->uuidGenerator,
			$this->eventRepository,
			$this->eventBus
		);
		$handler($command);
	}

	/**
	 * @param Uuid $id
	 * @return DeckIdDTO
	 */
	private function createDeckDTO(Uuid $id): DeckIdDTO
	{
		$dto = new DeckIdDTO();
		$dto->setId($id);

		return $dto;
	}
}
