<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck\Command;

use App\Domain\Card\CardRepository;
use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Deck\Card\DeckCardDTO;
use App\Domain\Deck\Command\AddDeckCardCommand;
use App\Domain\Deck\Command\AddDeckCardHandler;
use App\Domain\Deck\Card\Card as DeckCard;
use App\Domain\Card\Card;
use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class AddDeckCardHandlerTest extends TestCase
{
	private DeckRepository $repository;
	private CardRepository $cardRepository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;
	private EventRepository $eventRepository;
	private EventBus $eventBus;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(DeckRepository::class);
		$this->cardRepository = $this->createMock(CardRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->eventRepository = $this->createMock(EventRepository::class);
		$this->eventBus = $this->createMock(EventBus::class);
		$this->uuidGenerator = new UuidGenerator();
	}

	public function dataProviderDeckCardAdd(): array
	{
		return [
			'add new' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 1,
				'cards' => [],
			],
			'increase amount' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 1,
				'cards' => [
					['id' => '4fdec374-833d-485c-a142-eeeb30d733b1', 'title' => 'Any title string', 'amount' => 1, 'power' => 3],
				],
			]
		];
	}

	/**
	 * @dataProvider dataProviderDeckCardAdd
	 * @param string $userId
	 * @param string $deckId
	 * @param string $cardId
	 * @param int $amount
	 * @param array $itemsToAdd
	 */
	public function testHandle(
		string $userId,
		string $deckId,
		string $cardId,
		int $amount,
		array $itemsToAdd
	)
	{
		$command = new AddDeckCardCommand($this->createDeckCardDTO($deckId, $cardId, $amount));
		$dto = $command->getDto();
		$this->validator->expects(self::once())->method('validate')->with($dto);

		$items = [];
		foreach ($itemsToAdd as $item) {
			$items[$item['id']] = DeckCard::createCard(
				$item['id'],
				'Any title ' . $item['id'],
				$item['power'] ?? 1,
				$item['amount'] ?? 1
			);
		}
		$model = Deck::buildDeck(
			$deckId,
			$userId,
			$items
		);
		$this->repository->expects(self::once())
			->method('getById')
			->with($deckId)
			->willReturn($model);

		$card = Card::createCard($cardId, 'Any title ' . $cardId, 3);
		$this->cardRepository->expects(self::once())
			->method('getById')
			->with($cardId)
			->willReturn($card);

		$this->repository->expects(self::once())
			->method('saveAggregateCards');

		$handler = new AddDeckCardHandler(
			$this->repository,
			$this->cardRepository,
			$this->validator,
			$this->uuidGenerator,
			$this->eventRepository,
			$this->eventBus
		);
		$handler($command);
	}

	public function dataProviderDeckCardAddException(): array
	{
		return [
			'add not existing card to deck' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 1,
			]
		];
	}

	/**
	 * @dataProvider dataProviderDeckCardAddException
	 * @param string $userId
	 * @param string $deckId
	 * @param string $cardId
	 * @param int $amount
	 */
	public function testAddItemException(
		string $userId,
		string $deckId,
		string $cardId,
		int $amount
	)
	{
		$cardDTO = $this->createDeckCardDTO($deckId, $cardId, $amount);
		$command = new AddDeckCardCommand($cardDTO);
		$dto = $command->getDto();
		$this->validator->expects(self::once())->method('validate')->with($dto);

		$deck = Deck::buildDeck($deckId, $userId, []);

		$this->repository->expects(self::once())
			->method('getById')
			->with($deckId)
			->willReturn($deck);

		$this->cardRepository->expects(self::once())
			->method('getById')
			->with($cardId)
			->willThrowException(new NotFoundException(sprintf("No card with id %s exists", $cardId)));

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage(sprintf("No card with id %s exists", $cardId));

		$handler = new AddDeckCardHandler(
			$this->repository,
			$this->cardRepository,
			$this->validator,
			$this->uuidGenerator,
			$this->eventRepository,
			$this->eventBus
		);
		$handler($command);
	}

	/**
	 * @param string $deckId
	 * @param string $cardId
	 * @param int $amount
	 * @return DeckCardDTO
	 */
	public function createDeckCardDTO(string $deckId, string $cardId, int $amount): DeckCardDTO
	{
		$dto = new DeckCardDTO();
		$dto->setDeckId($this->uuidGenerator->fromString($deckId));
		$dto->setCardId($this->uuidGenerator->fromString($cardId));
		$dto->setAmount($amount);

		return $dto;
	}
}
