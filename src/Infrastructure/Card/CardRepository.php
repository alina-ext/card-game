<?php

namespace App\Infrastructure\Card;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Exceptions\ConflictException;
use App\Domain\Card\Exceptions\DBException;
use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class CardRepository extends ServiceEntityRepository implements CardRepositoryInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Card::class);
	}

	public function save(CardDTO $cardDTO): void
	{
		$em = $this->getEntityManager();

		$card = new Card();
		$card->setId($cardDTO->getId()->jsonSerialize())->setTitle($cardDTO->getTitle())->setPower($cardDTO->getPower());

		try {
			$em->persist($card);
			$em->flush();
		} catch (UniqueConstraintViolationException $e) {
			throw new ConflictException(
				sprintf("Card with title %s already exists", $cardDTO->getTitle())
			);
		} catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e);
		}
	}
}