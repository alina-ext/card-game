<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardDTO;
use App\Domain\Card\CardRepository;
use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Command\AddCardHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Validator;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AddCardHandlerTest extends TestCase
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
		$command = new AddCardCommand($this->createCardDTO($this->uuidGenerator->generate(), 'title', 1));
		$dto = $command->getDto();
		$model = Card::createCard(
			$this->uuidGenerator->toString($dto->getId()),
			$dto->getTitle(),
			$dto->getPower()
		);

		$this->validator->expects(self::once())->method('validate')->with($dto);
		$this->repository->expects(self::once())
			->method('save')
			->with($model);

		$handler = new AddCardHandler(
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
	 * @param string $title
	 * @param int $power
	 * @return CardDTO
	 */
	private function createCardDTO(Uuid $uuid, string $title, int $power): CardDTO
	{
		$dto = new CardDTO();
		$dto->setId($uuid);
		$dto->setTitle($title);
		$dto->setPower($power);

		return $dto;
	}
}
