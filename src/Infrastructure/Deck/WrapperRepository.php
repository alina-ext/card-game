<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck;

use App\Domain\Deck\Deck as DeckModel;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Entity\Deck;
use App\Infrastructure\Common\Event\EventRepositoryInterface;
use App\Infrastructure\Common\Event\Publisher;

class WrapperRepository implements DeckRepositoryInterface
{
	private DeckRepositoryInterface $deckRepository;
	private EventRepositoryInterface $eventRepository;
	private Publisher $publisher;

	public function __construct(
		DeckRepositoryInterface $deckRepository,
		EventRepositoryInterface $eventRepository, 
		Publisher $publisher
	)
	{
		$this->deckRepository = $deckRepository;
		$this->eventRepository = $eventRepository;
		$this->publisher = $publisher;
	}

	public function save(DeckModel $deck): Deck
	{
		$events = $deck->getEvents();
		foreach ($events as $event) {
			$this->eventRepository->push($event);
		}
		$model = $this->deckRepository->save($deck);
		foreach ($events as $event) {
			$this->publisher->publish($event);
		}
		$deck->deleteEvents();

		return $model;
	}

	public function saveCard(DeckModel $deck): void {
		$events = $deck->getEvents();
		foreach ($events as $event) {
			$this->eventRepository->push($event);
		}
		$this->deckRepository->saveCard($deck);
		foreach ($events as $event) {
			$this->publisher->publish($event);
		}
		$deck->deleteEvents();
	}

	public function getById(string $id): DeckModel
	{
		return $this->deckRepository->getById($id);
	}
}