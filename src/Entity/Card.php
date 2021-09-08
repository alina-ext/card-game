<?php
declare(strict_types=1);

namespace App\Entity;

use App\Infrastructure\Card\CardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @ORM\Table(name="cards")
 */
class Card
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36, unique=true)
	 */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $title;

    /**
     * @ORM\Column(type="smallint", columnDefinition="unsigned")
     */
    private int $power;

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): self
	{
		$this->id = $id;

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
}
