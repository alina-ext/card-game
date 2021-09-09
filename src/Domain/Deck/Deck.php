<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Domain\Deck\Card\Card;
use App\Domain\Deck\Exceptions\DeckSizeLimitReachedException;
use App\Entity\Event;
use DateTime;
use App\Domain\Deck\Card\Response AS CardResponse;

class Deck
{
	private const DECK_SIZE_LIMIT = 10;
	private const DECK_CARD_LIMIT = 2;

	private string $id;
	private string $userId;
	/** @var Event[] */
	private array $events;
	/** @var Card[] */
	private array $cards;

	public function __construct(string $id, string $userId, array $cards = [])
	{
		$this->id = $id;
		$this->userId = $userId;
		$this->events = [];
		$this->cards = [];
		if ($cards) {
			foreach ($cards as $card) {
				$this->cards[$card->getCardId()] = new Card($card->getCardId(), $card->getTitle(), $card->getPower(), $card->getAmount());
			}
		}
	}

	public function addCard(Card $card): void
	{
		if (!$this->canBeAdded($card)) {
			throw new DeckSizeLimitReachedException(sprintf('Unable to add new cards to deck %s', $this->id));
		}

		if (array_key_exists($card->getId(), $this->cards)) {
			$card = $this->cards[$card->getId()];
			$card->setAmount($card->getAmount() + $card->getAmount());
		} else {
			$this->cards[$card->getId()] = $card;
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

		return $this->getTotalCardsAmount() < self::DECK_SIZE_LIMIT;
	}

	private function getTotalCardsAmount(): int
	{
		return count($this->cards);
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

	public function pushEvent(string $eventTitle)
	{
		$event = new Event();
		$event->setTitle($eventTitle);
		$event->setData(json_encode($this->getDeck()));
		$event->setTm(new DateTime());
		$this->events[] = $event;
	}

	public function getEvents(): array
	{
		return $this->events;
	}

	public function deleteEvents(): void
	{
		$this->events = [];
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
}