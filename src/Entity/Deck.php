<?php
declare(strict_types=1);

namespace App\Entity;

use App\Infrastructure\Repository\DeckRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeckRepository::class)
 * @ORM\Table(name="decks")
 */
class Deck
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36, unique=true, nullable=false)
	 */
	private string $id;

	/**
	 * @ORM\Column(type="string", length=36, nullable=false)
	 */
	private string $user_id;

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getUserId(): string
	{
		return $this->user_id;
	}

	public function setUserId(string $userId): self
	{
		$this->user_id = $userId;

		return $this;
	}
}
