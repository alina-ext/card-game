<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Card\CardCollection;
use App\Domain\Card\CardRepository as ICardRepository;
use App\Domain\Card\Exceptions\ConflictException;
use App\Domain\Card\Exceptions\DBException;
use App\Entity\Card;
use App\Infrastructure\Card\FilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Card\Card as CardModel;

class CardRepository extends ServiceEntityRepository implements ICardRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Card::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(CardModel $card): void
	{
		$em = $this->getEntityManager();
		$entity = $this->find($card->getId());

		if ($card->isDeleted()) {
			if ($entity) {
				$em->remove($entity);
				$em->flush();
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

	/**
	 * {@inheritDoc}
	 */
	public function getByIds(array $ids): array
	{
		$data = $this->findBy(['id' => $ids]);
		return array_reduce($data, function ($acc, $entity) {
			$acc[$entity->getId()] = CardModel::buildCard(
				$entity->getId(),
				$entity->getTitle(),
				$entity->getPower()
			);
			return $acc;
		}, []);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getById(string $id): CardModel
	{
		if (($entity = $this->find($id)) === null) {
			throw new NotFoundException(sprintf("No card with id %s exists", $id));
		}

		return CardModel::buildCard(
			$entity->getId(),
			$entity->getTitle(),
			$entity->getPower()
		);
	}

	/**
	 * {@inheritDoc}
	 */
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