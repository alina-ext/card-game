<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Card\CardRepository;
use App\Domain\Deck\Card\Card;
use App\Domain\Deck\Exceptions\NotFoundException;
use App\Domain\Deck\Card\DeckCardRepository as IDeckCardRepository;
use App\Entity\DeckCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeckCardRepository extends ServiceEntityRepository implements IDeckCardRepository
{
	private CardRepository $cardRepository;

	public function __construct(ManagerRegistry $registry, CardRepository $cardRepository)
	{
		$this->cardRepository = $cardRepository;
		parent::__construct($registry, DeckCard::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getByDeckId(string $deckId): array
	{
		if (($cards = $this->findBy(['deck_id' => $deckId])) === null) {
			throw new NotFoundException(sprintf("No deck with id %s exists", $deckId));
		}
		$ids = array_map(function ($card) {
			return $card->getCardId();
		}, $cards);
		$originalCards = $ids ? $this->cardRepository->getByIds($ids) : [];

		$result = [];
		foreach ($cards as $card) {
			$id = $card->getCardId();
			$result[$id] = Card::createCard(
				$id,
				$card->getTitle(),
				$card->getPower(),
				$card->getAmount(),
			);
			if (array_key_exists($id, $originalCards)) {
				$result[$id]->setOriginalData(false, $originalCards[$id]->getTitle(), $originalCards[$id]->getPower());
			} else {
				$result[$id]->setOriginalData(true);
			}

		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addToDeck(string $deckId, array $cards): void
	{
		if (!$cards) {
			return;
		}
		$em = $this->getEntityManager();
		foreach ($cards as $card) {
			$entity = new DeckCard();
			$entity->setDeckId($deckId);
			$entity->setCardId($card->getId());
			$entity->setTitle($card->getTitle());
			$entity->setPower($card->getPower());
			$entity->setAmount($card->getAmount());

			$em->persist($entity);
			$em->flush();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteInDeck(string $deckId, array $cards): void
	{
		if (!$cards) {
			return;
		}
		$this->getEntityManager()->createQueryBuilder()
			->delete('App\Entity\DeckCard', 'DC')
			->where('DC.deck_id = :deck_id AND DC.card_id IN (:card_ids)')
			->setParameter('deck_id', $deckId)
			->setParameter('card_ids', array_keys($cards))
			->getQuery()->execute();
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateInDeck(string $deckId, array $cards): void
	{
		if (!$cards) {
			return;
		}
		$queryBuilder = $this->getEntityManager()->createQueryBuilder();
		foreach ($cards as $card) {
			$queryBuilder->update('App\Entity\DeckCard', 'DC')
				->set('DC.title', ':title')
				->set('DC.power', ':power')
				->set('DC.amount', ':amount')
				->where('DC.deck_id = :deck_id AND DC.card_id = :card_id')
				->setParameter('title', $card->getTitle())
				->setParameter('power', $card->getPower())
				->setParameter('amount', $card->getAmount())
				->setParameter('deck_id', $deckId)
				->setParameter('card_id', $card->getId())
				->getQuery()->execute();
		}
	}
}