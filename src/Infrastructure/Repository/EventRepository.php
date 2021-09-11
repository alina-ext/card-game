<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Card\Exceptions\DBException;
use App\Infrastructure\Common\EventRepository as IEventRepository;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use App\Infrastructure\Common\Event\Event as DomainEvent;
use DateTime;

class EventRepository extends ServiceEntityRepository implements IEventRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Event::class);
	}

	public function save(DomainEvent $event): void
	{
		$entity = new Event();
		$entity->setTitle($event->getTitle());
		$entity->setData(json_encode($event->getData()));
		$entity->setTm(new DateTime());

		$em = $this->getEntityManager();
		try {
			$em->persist($entity);
		} catch (Exception $e) {
			throw new DBException($e->getMessage());
		}
	}
}