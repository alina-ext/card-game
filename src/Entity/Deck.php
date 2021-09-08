<?php
declare(strict_types=1);

namespace App\Entity;

use App\Infrastructure\Deck\DeckRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeckRepository::class)
 * @ORM\Table(name="decks")
 */
class Deck
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36, unique=true)
	 */
	private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_id;

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

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
