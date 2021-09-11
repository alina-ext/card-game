<?php
declare(strict_types=1);

namespace App\Entity;

use App\Infrastructure\Repository\DeckCardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeckCardRepository::class)
 * @ORM\Table(name="decks_cards")
 */
class DeckCard
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36, nullable=false)
	 */
	private string $deck_id;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36, nullable=false)
	 */
	private string $card_id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private string $title;

	/**
	 * @ORM\Column(type="smallint", columnDefinition="unsigned", nullable=false)
	 */
	private int $power;

	/**
	 * @ORM\Column(type="smallint", columnDefinition="unsigned", nullable=false)
	 */
	private int $amount;

	public function getDeckId()
	{
		return $this->deck_id;
	}

	public function setDeckId(string $deckId): self
	{
		$this->deck_id = $deckId;

		return $this;
	}

	public function getCardId()
	{
		return $this->card_id;
	}

	public function setCardId(string $cardId): self
	{
		$this->card_id = $cardId;

		return $this;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	public function getPower(): int
	{
		return $this->power;
	}

	public function setPower(int $power): self
	{
		$this->power = $power;

		return $this;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function setAmount(int $amount): self
	{
		$this->amount = $amount;

		return $this;
	}
}
