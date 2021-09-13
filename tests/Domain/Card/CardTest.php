<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card;

use App\Domain\Card\Card;
use App\Domain\Card\CardEditDTO;
use App\Domain\Card\Event\Event;
use App\Domain\Card\Response;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\UuidGenerator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
	private UuidGenerator $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function setUp(): void
	{
		parent::setUp();
		$this->eventRepository = $this->createMock(EventRepository::class);
		$this->eventBus = $this->createMock(EventBus::class);
	}

	public function dataProviderCreate(): array
	{

		$uuid = $this->generateUUID();
		return [
			'get-card-created' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'deleted' => false,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'Gerald',
					'power' => 10,
					'events' => [
						$this->generateEvent('card:add', $this->uuidToString($uuid), 'Gerald', 10),
					]
				],
			],
			'get-card-deleted' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'deleted' => true,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'Gerald',
					'power' => 10,
					'events' => [
						$this->generateEvent('card:delete', $this->uuidToString($uuid), 'Gerald', 10),
					]
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderCreate
	 * @param Uuid $id
	 * @param string $title
	 * @param int $power
	 * @param bool $deleted
	 * @param array $expected
	 */
	public function testCreateCard(
		Uuid $id,
		string $title,
		int $power,
		bool $deleted,
		array $expected
	)
	{
		$card = Card::createCard($this->uuidToString($id), $title, $power, $deleted);

		self::assertEquals($expected, [
			'id' => $card->getId(),
			'title' => $card->getTitle(),
			'power' => $card->getPower(),
			'events' => $this->getPrivateProperty($card, 'events')
		]);
	}

	public function dataProviderUpdate(): array
	{

		$uuid = $this->generateUUID();
		return [
			'changing-title-power' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => 'my-Gerald',
				'setPower' => 20,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'my-Gerald',
					'power' => 20,
					'events' => [
						$this->generateEvent('card:update:title', $this->uuidToString($uuid), 'my-Gerald', 10),
						$this->generateEvent('card:update:power', $this->uuidToString($uuid), 'my-Gerald', 20),
					]
				],
			],
			'changing-title' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => 'my-Gerald',
				'setPower' => null,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'my-Gerald',
					'power' => 10,
					'events' => [
						$this->generateEvent('card:update:title', $this->uuidToString($uuid), 'my-Gerald', 10),
					]
				]
			],
			'changing-power' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => null,
				'setPower' => 20,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'Gerald',
					'power' => 20,
					'events' => [
						$this->generateEvent('card:update:power', $this->uuidToString($uuid), 'Gerald', 20),
					]
				]
			],
		];
	}

	/**
	 * @dataProvider dataProviderUpdate
	 * @param Uuid $id
	 * @param string $title
	 * @param int $power
	 * @param string|null $setTitle
	 * @param int|null $setPower
	 * @param array $expected
	 */
	public function testUpdate(
		Uuid $id,
		string $title,
		int $power,
		?string $setTitle,
		?int $setPower,
		array $expected
	)
	{
		$cardDTO = $this->createCardDTO($id, $setTitle, $setPower);

		$card = Card::buildCard($this->uuidToString($id), $title, $power);
		$card->update($cardDTO);

		self::assertEquals($expected, [
			'id' => $card->getId(),
			'title' => $card->getTitle(),
			'power' => $card->getPower(),
			'events' => $this->getPrivateProperty($card, 'events')
		]);
	}

	public function dataProviderResponse(): array
	{
		return [
			'response' => [
				'id' => '0a6f9848-2e9b-4f4c-9001-60d8d7ca4b14',
				'title' => 'My-title',
				'power' => 10,
				'expected' => [
					'id' => '0a6f9848-2e9b-4f4c-9001-60d8d7ca4b14',
					'title' => 'My-title',
					'power' => 10
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderResponse
	 * @param string $id
	 * @param string $title
	 * @param int $power
	 * @param array $expectedResponse
	 */
	public function testFillResponse(
		string $id,
		string $title,
		int $power,
		array $expectedResponse
	)
	{
		$card = Card::buildCard($id, $title, $power);

		$response = new Response();
		$card->fillResponse($response);

		self::assertEquals($expectedResponse, $response->getPayload());
	}

	public function dataProviderDispatch(): array
	{
		return [
			'card-create' => [
				'id' => '0a6f9848-2e9b-4f4c-9001-60d8d7ca4b14',
				'title' => 'Gerald',
				'power' => 10,
				'deleted' => false,
				'expected' => [],
			],
		];
	}

	/**
	 * @dataProvider dataProviderDispatch
	 * @param string $id
	 * @param string $title
	 * @param int $power
	 * @param bool $deleted
	 * @param array $expected
	 */
	public function testDispatch(string $id, string $title, int $power, bool $deleted, array $expected)
	{
		$event = $this->generateEvent('card:add', $id, $title, $power);
		$this->eventRepository->expects(self::once())
			->method('save')
			->with($event);
		$this->eventBus->expects(self::once())
			->method('dispatch')
			->with($event);

		$card = Card::createCard($id, $title, $power, $deleted);
		$card->dispatch($this->eventRepository, $this->eventBus);

		self::assertEquals($expected, $this->getPrivateProperty($card, 'events'));
	}

	/**
	 * @param Uuid $uuid
	 * @return string
	 */
	private function uuidToString(Uuid $uuid): string
	{
		return $uuid->jsonSerialize();
	}

	/**
	 * @return Uuid
	 */
	private function generateUUID(): Uuid
	{
		if (!isset($this->uuidGenerator)) {
			$this->uuidGenerator = new UuidGenerator();
		}

		return $this->uuidGenerator->generate();
	}

	/**
	 * @param Uuid $uuid
	 * @param string|null $title
	 * @param int|null $power
	 * @return CardEditDTO
	 */
	private function createCardDTO(Uuid $uuid, ?string $title, ?int $power): CardEditDTO
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

	/**
	 * @param string $eventTitle
	 * @param string $cardId
	 * @param string $cardTitle
	 * @param int $cardPower
	 * @return Event
	 */
	private function generateEvent(string $eventTitle, string $cardId, string $cardTitle, int $cardPower): Event
	{
		$event = new Event($eventTitle);
		$event->setCardId($cardId);
		$event->setCardTitle($cardTitle);
		$event->setCardPower($cardPower);

		return $event;
	}

	/**
	 * @param Card $object
	 * @param string $property
	 * @return mixed
	 * @throws ReflectionException
	 */
	private function getPrivateProperty(Card $object, string $property): mixed
	{
		$reflectedClass = new ReflectionClass($object);
		$reflection = $reflectedClass->getProperty($property);
		$reflection->setAccessible(true);

		return $reflection->getValue($object);
	}
}
