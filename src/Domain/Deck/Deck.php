<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Domain\Deck\Card\Card;
use App\Domain\Deck\Exceptions\DeckSizeLimitReachedException;
use App\Domain\Deck\Exceptions\NotEnoughCards;
use App\Domain\Deck\Event\Event as DeckEvent;
use App\Domain\Deck\Card\Event\Event as DeckCardEvent;
use App\Infrastructure\Common\Event\Event as DomainEvent;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;
use App\Domain\Deck\Card\Response as CardResponse;

class Deck
{
	private const DECK_SIZE_LIMIT = 10;
	private const DECK_CARD_LIMIT = 2;

	private string $id;
	private string $userId;
	private bool $deleted;
	/** @var DomainEvent[] */
	private array $events;
	/** @var Card[] */
	private array $cards;

	private function __construct(string $id, string $userId, array $cards = [])
	{
		$this->id = $id;
		$this->userId = $userId;
		$this->events = [];
		$this->cards = $cards;
		$this->deleted = false;
	}

	public static function createDeck(string $id, string $userId): Deck {
		$deck = self::buildDeck($id, $userId);
		$deck->pushDeckEvent('deck:add');

		return $deck;
	}

	public static function buildDeck(string $id, string $userId, array $cards = []): Deck {
		return new self($id, $userId, $cards);
	}

	private function pushDeckEvent(string $title)
	{
		$event = new DeckEvent($title);
		$event->setDeckId($this->id);
		$event->setUserId($this->userId);
		$this->events[] = $event;
	}

	private function pushDeckCardEvent(string $title, Card $card)
	{
		$event = new DeckCardEvent($title);
		$event->setDeckId($this->id);
		$event->setCardId($card->getId());
		$event->setCardTitle($card->getTitle());
		$event->setCardPower($card->getPower());
		$event->setCardAmount($card->getAmount());
		$this->events[] = $event;
	}

	public function addCard(Card $card): void
	{
		if (!$this->canBeAdded($card)) {
			throw new DeckSizeLimitReachedException(sprintf('Unable to add new cards to deck %s', $this->id));
		}

		if (array_key_exists($card->getId(), $this->cards)) {
			$cardExisted = $this->cards[$card->getId()];
			$cardExisted->setAmount($card->getAmount() + $cardExisted->getAmount());
		} else {
			$this->cards[$card->getId()] = $card;
		}
		$this->pushDeckCardEvent('deck:card:add', $card);
	}

	public function deleteCards(string $cardId, int $amount): void
	{
		if (array_key_exists($cardId, $this->cards)) {
			$card = $this->cards[$cardId];
			$newAmount = $card->getAmount() - $amount;
			if ($newAmount < 0) {
				throw new NotEnoughCards(sprintf('Not enough card %s in deck to delete', $cardId));
			} else if (!$newAmount) {
				unset($this->cards[$cardId]);
				$this->pushDeckCardEvent('deck:card:delete', $card);
			} else {
				//only for correct event amount
				$card->setAmount($amount);
				$this->pushDeckCardEvent('deck:card:delete', $card);

				$card->setAmount($newAmount);
			}
		} else {
			throw new NotEnoughCards(sprintf('No cards %s in deck', $cardId));
		}
	}

	/**
	 * @return Card[]
	 */
	public function getCards(): array
	{
		return $this->cards;
	}

	private function canBeAdded(Card $card): bool
	{
		$uniqAmount = $this->getUniqueCardAmount($card->getId());
		if ($uniqAmount) {
			return $uniqAmount + $card->getAmount() <= self::DECK_CARD_LIMIT;
		}

		return $this->getTotalCardsAmount() + $card->getAmount() <= self::DECK_SIZE_LIMIT;
	}

	private function getTotalCardsAmount(): int
	{
		return array_reduce($this->cards, function ($acc, $card) {
			$acc += $card->getAmount();
			return $acc;
		}, 0);
	}

	private function getUniqueCardAmount(string $id): int
	{
		return isset($this->cards[$id]) ? $this->cards[$id]->getAmount() : 0;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getUserId(): string
	{
		return $this->userId;
	}

	public function getDeck()
	{
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'cards' => $this->cards
		];
	}

	private function getPower(): int
	{
		$result = 0;
		foreach ($this->cards as $card) {
			$result += ($card->getAmount() * $card->getPower());
		}

		return $result;
	}

	public function dispatch(EventRepository $eventRepository, EventBus $eventBus)
	{
		foreach ($this->events as $event) {
			$eventRepository->save($event);
			if (in_array($event->getTitle(), ['deck:card:add', 'deck:card:delete', 'deck:delete'])) {
				$eventBus->dispatch($event);
			}
		}
		$this->events = [];
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setUserId($this->userId);
		$cards = [];
		foreach ($this->cards as $card) {
			$cardResponse = new CardResponse();
			$card->fillResponse($cardResponse);
			$cards[] = $cardResponse->getPayload();
		}
		$response->setCards($cards);
		$response->setPower($this->getPower());
	}

	public function setDeleted(): void
	{
		$this->deleted = true;
		if ($this->cards) {
			foreach ($this->cards as $card) {
				$this->pushDeckCardEvent('deck:card:delete', $card);
			}
		}
		$this->pushDeckEvent('deck:delete');
	}

	public function isDeleted(): bool
	{
		return $this->deleted;
	}
}