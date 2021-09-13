<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardIdDTO;
use App\Domain\Card\CardRepository;
use App\Domain\Card\Command\DeleteCardCommand;
use App\Domain\Card\Command\DeleteCardHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DeleteCardHandlerTest extends TestCase
{
	private CardRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(CardRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->eventRepository = $this->createMock(EventRepository::class);
		$this->eventBus = $this->createMock(EventBus::class);
		$this->uuidGenerator = new UuidGenerator();
	}

	public function testHandle()
	{
		$command = new DeleteCardCommand($this->createCardDTO($this->uuidGenerator->generate()));
		$dto = $command->getDto();
		$model = Card::createCard(
			$this->uuidGenerator->toString($dto->getId()),
			'',
			0,
			true
		);

		$this->validator->expects(self::once())->method('validate')->with($dto);
		$this->repository->expects(self::once())
			->method('save')
			->with($model);

		$handler = new DeleteCardHandler(
			$this->repository,
			$this->validator,
			$this->uuidGenerator,
			$this->eventRepository,
			$this->eventBus
		);
		$handler($command);
	}

	/**
	 * @param Uuid $uuid
	 * @return CardIdDTO
	 */
	private function createCardDTO(Uuid $uuid): CardIdDTO
	{
		$dto = new CardIdDTO();
		$dto->setId($uuid);

		return $dto;
	}
}
