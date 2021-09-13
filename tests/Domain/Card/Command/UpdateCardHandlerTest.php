<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card\Command;

use App\Domain\Card\Card;
use App\Domain\Card\CardEditDTO;
use App\Domain\Card\CardRepository;
use App\Domain\Card\Command\UpdateCardCommand;
use App\Domain\Card\Command\UpdateCardHandler;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCardHandlerTest extends TestCase
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
		$id = $this->uuidGenerator->generate();
		$dto = $this->createCardDTO($id, 'my-title');
		$command = new UpdateCardCommand($dto);

		$model = Card::buildCard(
			$this->uuidGenerator->toString($id),
			'my-title',
			10
		);

		$this->validator->expects(self::once())->method('validate')->with($dto);
		$this->repository->expects(self::once())
			->method('getById')
			->with($this->uuidGenerator->toString($id))
			->willReturn($model);

		$model->update($dto);

		$this->repository->expects(self::once())
			->method('save')
			->with($model);

		$handler = new UpdateCardHandler(
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
	 * @param string|null $title
	 * @param int|null $power
	 * @return CardEditDTO
	 */
	private function createCardDTO(Uuid $uuid, ?string $title = null, ?int $power = null): CardEditDTO
	{
		$dto = new CardEditDTO();
		$dto->setId($uuid);
		if (null !== $title) {
			$dto->setTitle($title);
		}
		if (null !== $power) {
			$dto->setPower($power);
		}

		return $dto;
	}
}
