<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck\Card;

use App\Domain\Deck\Exceptions\NotFoundException;
use App\Domain\Deck\Card\DeckCardRepositoryInterface;
use App\Entity\DeckCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeckCardRepository extends ServiceEntityRepository implements DeckCardRepositoryInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeckCard::class);
	}

	public function getByDeckId(string $deckId): array {
		if (($cards = $this->findBy(['deck_id' => $deckId])) === null) {
			throw new NotFoundException(sprintf("No deck with id %s exists", $deckId));
		}

		return $cards;
	}

	public function deleteByDeckId(string $deckId): void
	{
		// TODO: Implement deleteByDeckId() method.
	}

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