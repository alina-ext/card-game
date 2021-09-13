<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Deck\Card\DeckCardRepository;
use App\Domain\Deck\Exceptions\DBException;
use App\Domain\Deck\DeckRepository as IDeckRepository;
use App\Entity\Deck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Domain\Deck\Deck as DeckModel;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class DeckRepository extends ServiceEntityRepository implements IDeckRepository
{
	private DeckCardRepository $deckCardRepository;

	public function __construct(ManagerRegistry $registry, DeckCardRepository $deckCardRepository)
	{
		$this->deckCardRepository = $deckCardRepository;
		parent::__construct($registry, Deck::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(DeckModel $deck): void
	{
		$em = $this->getEntityManager();
		if ($deck->isDeleted()) {
			$this->deckCardRepository->deleteInDeck($deck->getId(), $deck->getCards());
			$deckEntity = $this->find($deck->getId());
			$em->remove($deckEntity);
			$em->flush();
			return;
		}
		$entity = new Deck();
		$entity
			->setId($deck->getId())
			->setUserId($deck->getUserId());
		try {
			$em->persist($entity);
			$em->flush();
		} catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * {@inheritDoc}
	 */
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

	/**
	 * {@inheritDoc}
	 */
	public function getById(string $id): DeckModel
	{
		if (($deck = $this->find($id)) === null) {
			throw new NotFoundException(sprintf("No deck with id %s exists", $id));
		}

		return DeckModel::buildDeck($deck->getId(), $deck->getUserId(), $this->deckCardRepository->getByDeckId($id));
	}
}