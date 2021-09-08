<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Entity\Event;
use DateTime;
use App\Entity\Card as CardEntity;

class Card
{
	private string $id;
	private string $title;
	private int $power;
	private bool $deleted;
	/** @var Event[] */
	private array $events;
	private ?CardEntity $entity = null;

	public function __construct(string $id, string $title, int $power, bool $deleted = false)
	{
		$this->id = $id;
		$this->title = $title;
		$this->power = $power;
		$this->deleted = $deleted;
		$this->events = [];
	}

	public function pushEvent(string $eventTitle)
	{
		$event = new Event();
		$event->setTitle($eventTitle);
		$event->setData(json_encode($this->getCard()));
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

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getPower(): int
	{
		return $this->power;
	}

	public function setPower(int $power): void
	{
		$this->power = $power;
	}

	public function setEntity(CardEntity $entity): void
	{
		$this->entity = $entity;
	}

	public function getEntity(): ?CardEntity
	{
		return $this->entity;
	}

	public function isDeleted(): bool
	{
		return $this->deleted;
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
}