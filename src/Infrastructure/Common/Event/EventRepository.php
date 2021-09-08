<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Event;

use App\Domain\Card\Exceptions\DBException;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class EventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Event::class);
	}

	public function push(Event $event): void
	{
		$em = $this->getEntityManager();

		try {
			$em->persist($event);
		} catch (Exception $e) {
			throw new DBException($e->getMessage());
		}
	}
}