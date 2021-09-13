<?php
declare(strict_types=1);

namespace App\Tests\Domain\Deck\Query;

use App\Domain\Deck\Deck;
use App\Domain\Deck\DeckIdDTO;
use App\Domain\Deck\DeckRepository;
use App\Domain\Deck\Query\GetDeckHandler;
use App\Domain\Deck\Query\GetDeckQuery;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetDeckHandlerTest extends TestCase
{
	private DeckRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(DeckRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->uuidGenerator = new UuidGenerator();
	}

	public function testHandle()
	{
		$query = new GetDeckQuery($this->createDeckDTO($this->uuidGenerator->generate()));
		$dto = $query->getDto();
		$this->validator->expects(self::once())->method('validate')->with($dto);

		$userId = $this->uuidGenerator->toString($this->uuidGenerator->generate());
		$model = Deck::createDeck(
			$this->uuidGenerator->toString($dto->getId()),
			$userId
		);

		$this->repository->expects(self::once())
			->method('getById')
			->with($this->uuidGenerator->toString($dto->getId()))
			->willReturn($model);

		$handler = new GetDeckHandler(
			$this->validator,
			$this->repository,
			$this->uuidGenerator
		);
		$response = $handler($query);

		self::assertEquals([
			'id' => $this->uuidGenerator->toString($dto->getId()),
			'user_id' => $userId,
			'power' => 0,
			'cards' => []
		], $response->getPayload());
	}

	/**
	 * @param Uuid $id
	 * @return DeckIdDTO
	 */
	private function createDeckDTO(Uuid $id): DeckIdDTO
	{
		$dto = new DeckIdDTO();
		$dto->setId($id);

		return $dto;
	}
}
