<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Card\Event\Event;
use App\Infrastructure\Common\Event\Event as DomainEvent;
use App\Infrastructure\Common\Event\EventBus;
use App\Infrastructure\Common\EventRepository;

class Card
{
	private string $id;
	private string $title;
	private int $power;
	private bool $deleted;
	/** @var DomainEvent[] */
	private array $events;

	private function __construct(string $id, string $title, int $power, bool $deleted = false)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->deleted = $deleted;
		$this->events = [];
	}

	public static  function buildCard(string $id, string $title, int $power, bool $deleted = false) {
		return new self($id, $title, $power, $deleted);
	}

	public static function createCard(string $id, string $title, int $power, bool $deleted = false) {
		$card = self::buildCard($id, $title, $power, $deleted);
		if ($deleted) {
			$card->pushEvent('card:delete');
		} else {
			$card->pushEvent('card:add');
		}

		return $card;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getPower(): int
	{
		return $this->power;
	}

	public function isDeleted(): bool
	{
		return $this->deleted;
	}

	private function pushEvent(string $eventTitle): void
	{
		$event = new Event($eventTitle);
		$event->setCardId($this->id);
		$event->setCardTitle($this->title);
		$event->setCardPower($this->power);
		$this->events[] = $event;
	}

	public function update(CardEditDTO $dto) {
		if (null !== ($title = $dto->getTitle())) {
			$this->title = $title;
			$this->pushEvent('card:update:title');
		}
		if (null !== ($power = $dto->getPower())) {
			$this->power = $power;
			$this->pushEvent('card:update:power');
		}
	}

	public function getCard()
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'power' => $this->power,
		];
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setTitle($this->title);
		$response->setPower($this->power);
	}

	public function dispatch(EventRepository $eventRepository, EventBus $eventBus)
	{
		foreach ($this->events as $event) {
			$eventRepository->save($event);
			$eventBus->dispatch($event);
		}
		$this->events = [];
	}
}