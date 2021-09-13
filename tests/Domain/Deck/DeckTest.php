<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck;

use App\Domain\Deck\Card\Card;
use App\Domain\Deck\Card\Event\Event as DeckCardEvent;
use App\Domain\Deck\Deck;
use App\Domain\Deck\Event\Event;
use App\Domain\Deck\Exceptions\DeckSizeLimitReachedException;
use App\Domain\Deck\Exceptions\NotEnoughCards;
use App\Domain\Deck\Response;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\UuidGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Uid\Uuid;

class DeckTest extends TestCase
{
	private const DECK_ID = '101010101-101010101-10101';
	private const USER_ID = '222222-222222-222';
	private const CARD_ID = '100500-100500-105000';

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
		$user_id = $this->generateUUID();
		return [
			'get-deck-created' => [
				'id' => $uuid,
				'user_id' => $user_id,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'user_id' => $this->uuidToString($user_id),
					'events' => [
						$this->generateEvent('deck:add', $this->uuidToString($uuid), $this->uuidToString($user_id)),
					]
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderCreate
	 * @param Uuid $id
	 * @param Uuid $userId
	 * @param array $expected
	 */
	public function testCreateDeck(
		Uuid $id,
		Uuid $userId,
		array $expected
	)
	{
		$deck = Deck::createDeck($this->uuidToString($id), $this->uuidToString($userId));

		self::assertEquals($expected, [
			'id' => $deck->getId(),
			'user_id' => $deck->getUserId(),
			'events' => $this->getPrivateProperty($deck, 'events')
		]);
	}

	public function dataProviderAddCard(): array
	{
		return [
			'add one card to deck' => [
				'id' => self::CARD_ID,
				'title' => 'Title 1',
				'power' => 10,
				'amount' => 1,
				'expected' => [
					'id' => [
						self::CARD_ID
					],
					'event' => [$this->generateCardEvent(
						'deck:card:add',
						self::DECK_ID,
						self::CARD_ID,
						'Title 1',
						10,
						1
					)],
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderAddCard
	 * @param string $id
	 * @param string $title
	 * @param int $power
	 * @param int $amount
	 * @param array $expected
	 */
	public function testAddItem(
		string $id,
		string $title,
		int $power,
		int $amount,
		array $expected
	)
	{
		$card = Card::createCard($id, $title, $power, $amount);
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID);
		$deck->addCard($card);

		$cardsFromDeck = $deck->getCards();
		$ids = [];
		foreach ($cardsFromDeck as $card) {
			$ids[] = $card->getId();
		}

		self::assertEquals($expected['id'], $ids);
		self::assertEquals($expected['event'], $this->getPrivateProperty($deck, 'events'));
	}


	public function dataProviderAddSameCard(): array
	{
		return [
			'add same card to deck' => [
				'id' => self::CARD_ID,
				'title' => 'Title 1',
				'power' => 10,
				'amount' => 1,
				'expected' => [
					self::CARD_ID => [
						'amount' => 2,
					],
				],
			],
		];
	}

	/**
	 * @dataProvider dataProviderAddSameCard
	 * @param string $id
	 * @param string $title
	 * @param int $power
	 * @param int $amount
	 * @param array $expected
	 */
	public function testAddSameItem(
		string $id,
		string $title,
		int $power,
		int $amount,
		array $expected
	)
	{
		$card = Card::createCard($id, $title, $power, $amount);
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, [
			$card->getId() => $card
		]);
		$deck->addCard($card);

		$cardsFromDeck = $deck->getCards();
		$cards = [];
		foreach ($cardsFromDeck as $card) {
			$cards[$card->getId()]['amount'] = $card->getAmount();
		}

		self::assertEquals($expected, $cards);
	}

	public function testAddItemException()
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, [
			'id1' => Card::createCard('id1', 'Title 1', 10, 2),
			'id2' => Card::createCard('id2', 'Title 1', 10, 2),
		]);

		self::expectException(DeckSizeLimitReachedException::class);
		$deck->addCard(Card::createCard('id1', 'Title 1', 10, 2));
	}

	public function dataProviderDeleteCards(): array
	{
		return [
			'full delete' => [
				'cardId' => self::CARD_ID,
				'amount' => 2,
				'expected' => [],
				'expectedEvents' => [
					$this->generateCardEvent(
						'deck:card:delete',
						self::DECK_ID,
						self::CARD_ID,
						'Title 1',
						10,
						2
					)
				],
			],
			'delete cards with rest' => [
				'cardId' => self::CARD_ID,
				'amount' => 1,
				'expected' => [
					self::CARD_ID => 1,
				],
				'expectedEvents' => [
					$this->generateCardEvent(
						'deck:card:delete',
						self::DECK_ID,
						self::CARD_ID,
						'Title 1',
						10,
						1
					)
				]
			],
		];
	}

	/**
	 * @dataProvider dataProviderDeleteCards
	 * @param string $cardId
	 * @param int $amount
	 * @param array $expectedAmount
	 * @param array $expectedEvents
	 * @throws ReflectionException
	 */
	public function testDeleteCards(string $cardId, int $amount, array $expectedAmount, array $expectedEvents)
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, [
			$cardId => Card::createCard($cardId, 'Title 1', 10, 2),
		]);
		$deck->deleteCards($cardId, $amount);

		$cardsFromDeck = $deck->getCards();
		$items = [];
		foreach ($cardsFromDeck as $card) {
			$items[$card->getId()] = $card->getAmount();
		}

		self::assertEquals($expectedAmount, $items);
		self::assertEquals($expectedEvents, $this->getPrivateProperty($deck, 'events'));
	}

	public function dataProviderDeleteCardsException(): array
	{
		return [
			'delete non existing card' => [
				'cardId' => self::CARD_ID,
				'amount' => 1,
				'expectedDeleteException' => NotEnoughCards::class,
			],
			'delete amount exceeded' => [
				'cardId' => self::CARD_ID . '-xxxx',
				'amount' => 3,
				'expectedDeleteException' => NotEnoughCards::class,
			],
		];
	}

	/**
	 * @dataProvider dataProviderDeleteCardsException
	 * @param string $cardId
	 * @param int $amount
	 * @param string $expectedDeleteException
	 */
	public function testDeleteCardsException(string $cardId, int $amount, string $expectedDeleteException)
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, [
			self::CARD_ID . '-yyyy' => Card::createCard(self::CARD_ID . '-yyyy', 'Title 1', 10, 1),
			self::CARD_ID . '-xxxx' => Card::createCard(self::CARD_ID . '-xxxx', 'Title 2', 10, 2)
		]);

		self::expectException($expectedDeleteException);
		$deck->deleteCards($cardId, $amount);
	}

	public function dataProviderFillResponse(): array
	{
		return [
			'response 1' => [
				[
					['id' => 'id1', 'title' => 'Any title id1', 'power' => 10, 'amount' => 1,],
					['id' => 'id1', 'title' => 'Any title id1', 'power' => 10, 'amount' => 1],
					['id' => 'id2', 'title' => 'Any title id2', 'power' => 20, 'amount' => 1],
				],
				'expectedResponse' => [
					'id' => self::DECK_ID,
					'user_id' => self::USER_ID,
					'power' => 40,
					'cards' => [
						[
							'id' => 'id1',
							'title' => 'Any title id1',
							'power' => 10,
							'amount' => 2,
						],
						[
							'id' => 'id2',
							'title' => 'Any title id2',
							'power' => 20,
							'amount' => 1,
						],
					],
				],
			],
			'response 2' => [
				[
					['id' => 'id1', 'title' => 'Any title id1', 'power' => 7, 'amount' => 3],
				],
				'expectedResponse' => [
					'id' => self::DECK_ID,
					'user_id' => self::USER_ID,
					'power' => 21,
					'cards' => [
						[
							'id' => 'id1',
							'title' => 'Any title id1',
							'power' => 7,
							'amount' => 3,
						],
					],
				],
			],
		];
	}

	public function dataProviderDispatch(): array
	{
		return [
			'deck-card-add' => [
				'id' => self::CARD_ID,
				'title' => 'Title 1',
				'power' => 10,
				'amount' => 1,
				'expected' => [],
			],
		];
	}

	/**
	 * @dataProvider dataProviderDispatch
	 * @param string $id
	 * @param string $title
	 * @param int $power
	 * @param int $amount
	 * @param array $expected
	 */
	public function testDispatch(string $id, string $title, int $power, int $amount, array $expected)
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, []);
		$deck->addCard(Card::createCard($id,
			$title,
			$power,
			$amount
		));

		$event = $this->generateCardEvent('deck:card:add',
			self::DECK_ID,
			$id,
			$title,
			$power,
			$amount);

		$this->eventRepository->expects(self::once())
			->method('save')
			->with($event);
		$this->eventBus->expects(self::once())
			->method('dispatch')
			->with($event);

		$deck->dispatch($this->eventRepository, $this->eventBus);

		self::assertEquals($expected, $this->getPrivateProperty($deck, 'events'));
	}

	/**
	 * @dataProvider dataProviderFillResponse
	 * @param array $cardsToAdd
	 * @param array $expectedResponse
	 */
	public function testFillResponse(array $cardsToAdd, array $expectedResponse)
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, []);
		foreach ($cardsToAdd as $card) {
			$deck->addCard(Card::createCard(...array_values($card)));
		}

		$response = new Response();
		$deck->fillResponse($response);

		self::assertEquals($expectedResponse, $response->getPayload());
	}

	public function testSetDeleted()
	{
		$deck = Deck::buildDeck(self::DECK_ID, self::USER_ID, [
			self::CARD_ID . '-yyyy' => Card::createCard(self::CARD_ID . '-yyyy', 'Title 1', 10, 1),
			self::CARD_ID . '-xxxx' => Card::createCard(self::CARD_ID . '-xxxx', 'Title 2', 10, 2)
		]);

		$deck->setDeleted();
		self::assertEquals($deck->isDeleted(), true);
		self::assertCount(3, $this->getPrivateProperty($deck, 'events'));
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
	 * @return string
	 */
	private function uuidToString(Uuid $uuid): string
	{
		return $uuid->jsonSerialize();
	}


	/**
	 * @param string $eventTitle
	 * @param string $deckId
	 * @param string $deckUserId
	 * @return Event
	 */
	private function generateEvent(string $eventTitle, string $deckId, string $deckUserId): Event
	{
		$event = new Event($eventTitle);
		$event->setDeckId($deckId);
		$event->setUserId($deckUserId);

		return $event;
	}

	/**
	 * @param string $eventTitle
	 * @param string $deckId
	 * @param string $cardId
	 * @param string $cardTitle
	 * @param int $cardPower
	 * @param int $cardAmount
	 * @return DeckCardEvent
	 */
	private function generateCardEvent(
		string $eventTitle,
		string $deckId,
		string $cardId,
		string $cardTitle,
		int $cardPower,
		int $cardAmount
	): DeckCardEvent
	{
		$event = new DeckCardEvent($eventTitle);
		$event->setDeckId($deckId);
		$event->setCardId($cardId);
		$event->setCardTitle($cardTitle);
		$event->setCardPower($cardPower);
		$event->setCardAmount($cardAmount);

		return $event;
	}

	/**
	 * @param Deck $object
	 * @param string $property
	 * @return mixed
	 * @throws ReflectionException
	 */
	private function getPrivateProperty(Deck $object, string $property): mixed
	{
		$reflectedClass = new ReflectionClass($object);
		$reflection = $reflectedClass->getProperty($property);
		$reflection->setAccessible(true);

		return $reflection->getValue($object);
	}
}
