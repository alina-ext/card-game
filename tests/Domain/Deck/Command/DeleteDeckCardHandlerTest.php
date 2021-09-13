<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck\Command;

use App\Domain\Deck\Card\Card as DeckCard;
use App\Domain\Deck\Card\DeckCardDTO;
use App\Domain\Deck\Command\DeleteDeckCardCommand;
use App\Domain\Deck\Command\DeleteDeckCardHandler;
use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckRepository;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class DeleteDeckCardHandlerTest extends TestCase
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

	public function dataProviderDeckCardDelete(): array
	{
		return [
			'delete line' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 2,
				'items' => [
					['id' => 'fe921b26-ff65-4f63-aa3b-f9dcb1a87abe', 'amount' => 1, 'power' => 2],
					['id' => '4fdec374-833d-485c-a142-eeeb30d733b1', 'amount' => 2, 'power' => 3],
				],
			],
			'all items' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 1,
				'items' => [
					['id' => '4fdec374-833d-485c-a142-eeeb30d733b1', 'amount' => 1, 'power' => 2],
				],
			],
			'decrease amount' => [
				'userId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'deckId' => '4fdec374-833d-485c-a142-eeeb30d733b2',
				'cardId' => '4fdec374-833d-485c-a142-eeeb30d733b1',
				'amount' => 1,
				'items' => [
					['id' => 'fe921b26-ff65-4f63-aa3b-f9dcb1a87abe', 'amount' => 1, 'power' => 1],
					['id' => '4fdec374-833d-485c-a142-eeeb30d733b1', 'amount' => 2, 'power' => 3],
				],
			]
		];
	}

	/**
	 * @dataProvider dataProviderDeckCardDelete
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
		$command = new DeleteDeckCardCommand($this->createDeckCardDTO($deckId, $cardId, $amount));
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

		$this->repository->expects(self::once())
			->method('saveAggregateCards')
			->with($model);

		$handler = new DeleteDeckCardHandler(
			$this->repository,
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
