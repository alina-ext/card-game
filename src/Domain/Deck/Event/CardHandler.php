<?php
declare(strict_types=1);

namespace App\Domain\Deck\Event;

use App\Infrastructure\Common\Event\Event;
use App\Infrastructure\Common\Event\EventHandler;
use App\Domain\Deck\Event\Event as DeckEvent;
use App\Domain\Deck\Card\Event\Event as DeckCardEvent;
use App\Domain\Card\Event\Event as CardEvent;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CardHandler implements EventHandler, MessageSubscriberInterface
{
	/* $data = ['card1' => ['deck1' => 'amount1', 'deck2' => 'amount2']] */
	/* Better to store it somewhere in redis for ex.*/
	private array $data = [];

	public function __invoke(Event $event): void
	{
		$eventTitle = $event->getTitle();
		switch ($eventTitle) {
			case 'card:update:title':
			case 'card:update:power':
			case 'card:delete':
				$this->notify($event);
				break;
			case 'deck:card:add':
			case 'deck:card:delete':
			case 'deck:delete':
				$this->subscribe($event);
				break;
		}
	}

	public function notify(Event $event): void
	{
		$eventTitle = $event->getTitle();
		switch ($eventTitle) {
			case 'card:update:title':
			case 'card:update:power':
			case 'card:delete':
				if (array_key_exists($event->getCardId(), $this->data)) {
					echo sprintf('Notify decks %s about changes in card %s', implode(', ', array_keys($this->data[$event->getCardId()])), $event->getCardId());
				}
				break;
		}
	}

	public function subscribe(Event $event): void
	{
		$eventTitle = $event->getTitle();
		switch ($eventTitle) {
			case 'deck:card:add':
				if (!empty($this->data[$event->getCardId()][$event->getDeckId()])) {
					$this->data[$event->getCardId()][$event->getDeckId()] += $event->getCardAmount();
				} else {
					$this->data[$event->getCardId()][$event->getDeckId()] = $event->getCardAmount();
				}
				break;
			case 'deck:card:delete':
				if (!empty($this->data[$event->getCardId()][$event->getDeckId()])) {
					$this->data[$event->getCardId()][$event->getDeckId()] -= $event->getCardAmount();
					if ($this->data[$event->getCardId()][$event->getDeckId()] <= 0) {
						unset($this->data[$event->getCardId()][$event->getDeckId()]);
					}
					if (empty($this->data[$event->getCardId()])) {
						unset($this->data[$event->getCardId()]);
					}
				}
				break;
			case 'deck:delete':
				foreach ($this->data as $cardId) {
					unset($this->data[$cardId][$event->getDeckId()]);
					if (empty($this->data[$cardId])) {
						unset($this->data[$cardId]);
					}
				}
				break;
		}
	}

	public static function getHandledMessages(): iterable
	{
		yield DeckEvent::class => [
			'method' => 'subscribe'
		];
		yield DeckCardEvent::class => [
			'method' => 'subscribe'
		];
		yield CardEvent::class => [
			'method' => 'notify'
		];
	}
}