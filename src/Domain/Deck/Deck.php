<?php
declare(strict_types=1);

namespace App\Domain\Deck;

use App\Entity\Event;
use DateTime;

class Deck
{
	private string $id;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string
	{
		return $this->userId;
	}
	private string $userId;
	/** @var Event[] */
	private array $events;

	public function __construct(string $id, string $userId)
	{
		$this->id = $id;
		$this->userId = $userId;
		$this->events = [];
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
		];
	}

	public function fillResponse(Response $response): void
	{
		$response->setId($this->id);
		$response->setUserId($this->userId);
	}
}