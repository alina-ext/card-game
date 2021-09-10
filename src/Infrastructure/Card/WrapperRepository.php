<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\Card as CardModel;
use App\Domain\Card\CardCollection;
use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Event\EventRepositoryInterface;
use App\Infrastructure\Common\Event\Publisher;

class WrapperRepository implements CardRepositoryInterface
{
	private CardRepositoryInterface $cardRepository;
	private EventRepositoryInterface $eventRepository;
	private Publisher $publisher;

	public function __construct(
		CardRepositoryInterface $cardRepository, 
		EventRepositoryInterface $eventRepository, 
		Publisher $publisher
	)
	{
		$this->cardRepository = $cardRepository;
		$this->eventRepository = $eventRepository;
		$this->publisher = $publisher;
	}

	public function save(CardModel $card): void
	{
		$events = $card->getEvents();
		foreach ($events as $event) {
			$this->eventRepository->push($event);
		}
		$this->cardRepository->save($card);
		//$card->dispatch($this->publisher);
		foreach ($events as $event) {
			$this->publisher->publish($event);
		}
		$card->deleteEvents();
	}

	public function getById(string $id): CardModel
	{
		return $this->cardRepository->getById($id);
	}

	public function getList(FilterService $filter): CardCollection
	{
		return $this->cardRepository->getList($filter);
	}
}