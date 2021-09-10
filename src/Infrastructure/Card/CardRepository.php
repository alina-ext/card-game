<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\CardCollection;
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

	public function save(CardModel $card): void
	{
		$em = $this->getEntityManager();
		$entity = $this->find($card->getId());

		if ($card->isDeleted()) {
			if ($entity) {
				$em->remove($entity);
				return;
			} else {
				throw new NotFoundException(sprintf('No card with id %s to delete', $card->getId()));
			}

		}
		if (!$entity) {
			$entity = new Card();
		}
		$entity
			->setId($card->getId())
			->setTitle($card->getTitle())
			->setPower($card->getPower());
		try {
			$em->persist($entity);
			$em->flush();
		} catch (UniqueConstraintViolationException $e) {
			throw new ConflictException(sprintf("Card with title %s already exists", $card->getTitle()));
		} catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function getById(string $id): CardModel
	{
		if (($entity = $this->find($id)) === null) {
			throw new NotFoundException(sprintf("No card with id %s exists", $id));
		}

		return new CardModel(
			$entity->getId(),
			$entity->getTitle(),
			$entity->getPower()
		);
	}

	public function getList(FilterService $filter): CardCollection
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("SELECT card FROM App\Entity\Card card");

		$query
			->setMaxResults($filter->getLimit())
			->setFirstResult($filter->getOffset());

		$data = $query->getResult();
		if (!$data) {
			throw new NotFoundException('Page not found');
		}

		$query = $em->createQuery("SELECT COUNT(card) FROM App\Entity\Card card");

		return new CardCollection($data, intval($query->getSingleScalarResult()));
	}
}