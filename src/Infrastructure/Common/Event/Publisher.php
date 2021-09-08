<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;
use App\Entity\Event;

class Publisher
{
	private array $subscribers;

	/**
	 * @param Subscriber[] $subscribers
	 */
	public function __construct()
	{
		$this->subscribers = [];
	}

	public function subscribe(Subscriber $subscriber) {
		$this->subscribers[] = $subscriber;
	}

	public function publish(Event $anEvent)
	{
		foreach($this->subscribers as $subscriber) {
			if ($subscriber->isSubscribed($anEvent)) {
				$subscriber->notify($anEvent);
			}
		}
	}
}