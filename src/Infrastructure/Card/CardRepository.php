<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Exceptions\ConflictException;
use App\Domain\Card\Exceptions\DBException;
use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Card\Card as CardModel;

class CardRepository extends ServiceEntityRepository implements CardRepositoryInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Card::class);
	}

	public function save(CardModel $card): Card
	{
		$em = $this->getEntityManager();
		$entity = $card->getEntity();

		if ($card->isDeleted()) {
			$em->remove($entity);
			return $entity;
		}
		$bAdd = false;
		if (!$entity) {
			$bAdd = true;
			$entity = new Card();
		}
		$entity
			->setId($card->getId())
			->setTitle($card->getTitle())
			->setPower($card->getPower());
		try {
			if ($bAdd) {
				$em->persist($entity);
			}
			$em->flush();
		} catch (UniqueConstraintViolationException $e) {
			throw new ConflictException(sprintf("Card with title %s already exists", $card->getTitle()));
		} catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e);
		}

		return $entity;
	}

	public function getById(string $id): Card
	{
		if (($entity = $this->find($id)) === null) {
			throw new NotFoundException(sprintf("No card with id %s exists", $id));
		}

		return $entity;
	}
}