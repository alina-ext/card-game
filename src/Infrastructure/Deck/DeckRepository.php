<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck;

use App\Domain\Deck\Exceptions\DBException;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Entity\Deck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Domain\Deck\Deck as DeckModel;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class DeckRepository extends ServiceEntityRepository implements DeckRepositoryInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Deck::class);
	}

	public function save(DeckModel $deck): Deck
	{
		$em = $this->getEntityManager();
		//$entity = $deck->getEntity();
		$entity = new Deck();
		$entity
			->setId($deck->getId())
			->setUserId($deck->getUserId());
		try {
			$em->persist($entity);
		} catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e);
		}

		return $entity;
	}
}