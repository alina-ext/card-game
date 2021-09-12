<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card;

use App\Domain\Card\Card;
use App\Domain\Card\CardEditDTO;
use App\Domain\Card\Event\Event;
use App\Infrastructure\Common\Generator\UuidGenerator;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
	private UuidGenerator $uuidGenerator;

	public function dataProviderUpdate(): array {

		$uuid = $this->generateUUID();
		return [
			'changing-title-power' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => 'my-Gerald',
				'setPower' => 20,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'my-Gerald',
					'power' => 20,
					'events' => [
						$this->generateEvent('card:update:title', $this->uuidToString($uuid), 'my-Gerald', 10),
						$this->generateEvent('card:update:power', $this->uuidToString($uuid), 'my-Gerald', 20),
					]
				],
			],
			'changing-title' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => 'my-Gerald',
				'setPower' => null,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'my-Gerald',
					'power' => 10,
					'events' => [
						$this->generateEvent('card:update:title', $this->uuidToString($uuid), 'my-Gerald', 10),
					]
				]
			],
			'changing-power' => [
				'id' => $uuid,
				'title' => 'Gerald',
				'power' => 10,
				'setTitle' => null,
				'setPower' => 20,
				'expected' => [
					'id' => $this->uuidToString($uuid),
					'title' => 'Gerald',
					'power' => 20,
					'events' => [
						$this->generateEvent('card:update:power', $this->uuidToString($uuid), 'Gerald', 20),
					]
				]
			],
		];
	}

	/**
	 * @dataProvider dataProviderUpdate
	 * @param Uuid $id
	 * @param string $title
	 * @param int $power
	 * @param string|null $setTitle
	 * @param int|null $setPower
	 * @param array $expected
	 */
	public function testUpdate (
		Uuid $id,
		string $title,
		int $power,
		?string $setTitle,
		?int $setPower,
		array $expected
	) {
		$cardDTO = $this->createCardDTO($id, $setTitle, $setPower);

		$card = Card::buildCard($this->uuidToString($id), $title, $power);
		$card->update($cardDTO);

		self::assertEquals($expected, [
			'id' => $card->getId(),
			'title' => $card->getTitle(),
			'power' => $card->getPower(),
			'events' => $this->getPrivateProperty($card, 'events')
		]);
	}

	private function uuidToString(Uuid $uuid): string {
		return $uuid->jsonSerialize();
	}

	private function generateUUID(): Uuid
	{
		if (!isset($this->uuidGenerator)) {
			$this->uuidGenerator = new UuidGenerator();
		}

		return $this->uuidGenerator->generate();
	}

	private function createCardDTO(Uuid $uuid, ?string $title, ?int $power): CardEditDTO {
		$dto = new CardEditDTO();
		$dto->setId($uuid);
		if (null !== $title) {
			$dto->setTitle($title);
		}
		if (null !== $power) {
			$dto->setPower($power);
		}

		return $dto;
	}

	private function generateEvent(string $eventTitle, string $cardId, string $cardTitle, int $cardPower): Event {
		$event = new Event($eventTitle);
		$event->setCardId($cardId);
		$event->setCardTitle($cardTitle);
		$event->setCardPower($cardPower);

		return $event;
	}

	private function getPrivateProperty(Card $object, string $property): mixed
	{
		$reflectedClass = new ReflectionClass($object);
		$reflection = $reflectedClass->getProperty($property);
		$reflection->setAccessible(true);

		return $reflection->getValue($object);
	}
}
