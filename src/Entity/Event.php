<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Repository\EventRepository;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(name="events")
 */
class Event
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private int $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private string $title;

	/**
	 * @ORM\Column(type="text")
	 */
	private string $data;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private DateTime $tm;

	public function getId(): int
	{
		return $this->id;
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

	public function getData(): string
	{
		return $this->title;
	}

	public function setData(string $data): self
	{
		$this->data = $data;

		return $this;
	}

	public function getTm(): DateTime
	{
		return $this->tm;
	}

	public function setTm(DateTime $tm): self
	{
		$this->tm = $tm;

		return $this;
	}
}