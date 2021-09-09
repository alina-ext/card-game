<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck;

use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Deck\Card\DeckCardRepositoryInterface;
use App\Domain\Deck\Exceptions\DBException;
use App\Domain\Deck\DeckRepositoryInterface;
use App\Entity\Deck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Domain\Deck\Deck as DeckModel;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class DeckRepository extends ServiceEntityRepository implements DeckRepositoryInterface
{
	private DeckCardRepositoryInterface $deckCardRepository;

	public function __construct(ManagerRegistry $registry, DeckCardRepositoryInterface $deckCardRepository)
	{
		$this->deckCardRepository = $deckCardRepository;
		parent::__construct($registry, Deck::class);
	}

	public function save(DeckModel $deck): Deck
	{
		$em = $this->getEntityManager();
		if ($deck->isDeleted()) {
			$this->deckCardRepository->deleteInDeck($deck->getId(), $deck->getCards());
			$deckEntity = $this->find($deck->getId());
			$em->remove($deckEntity);

			return $deckEntity;
		}
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

	public function saveCard(DeckModel $deck): void
	{
		$deckCurrentState = $this->getById($deck->getId());
		$cardsCurrentState = $deckCurrentState->getCards();
		$cardsToUpdate = [];
		$cardsFromDeck = $deck->getCards();

		$cardsToAdd = array_diff_key($cardsFromDeck, $cardsCurrentState);
		$cardsToDelete = array_diff_key($cardsCurrentState, $cardsFromDeck);

		//potential cards with updated amount
		$commonItems = array_intersect_key($cardsFromDeck, $cardsCurrentState);
		foreach ($commonItems as $card) {
			if ($card->getAmount() !== $cardsCurrentState[$card->getId()]->getAmount()) {
				$cardsToUpdate[] = $card;
			}
		}

		$this->deckCardRepository->addToDeck($deck->getId(), $cardsToAdd);
		$this->deckCardRepository->deleteInDeck($deck->getId(), $cardsToDelete);
		$this->deckCardRepository->updateInDeck($deck->getId(), $cardsToUpdate);
	}

	public function getById(string $id): DeckModel
	{
		if (($deck = $this->find($id)) === null) {
			throw new NotFoundException(sprintf("No deck with id %s exists", $id));
		}

		return new DeckModel($deck->getId(), $deck->getUserId(), $this->deckCardRepository->getByDeckId($id));
	}
}