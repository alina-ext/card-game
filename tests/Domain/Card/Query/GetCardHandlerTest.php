<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card\Query;

use App\Domain\Card\Card;
use App\Domain\Card\CardIdDTO;
use App\Domain\Card\CardRepository;
use App\Domain\Card\Query\GetCardHandler;
use App\Domain\Card\Query\GetCardQuery;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetCardHandlerTest extends TestCase
{
	private CardRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(CardRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->uuidGenerator = new UuidGenerator();
	}

	public function testHandle()
	{
		$query = new GetCardQuery($this->createCardDTO($this->uuidGenerator->generate()));
		$dto = $query->getDto();
		$model = Card::createCard(
			$this->uuidGenerator->toString($dto->getId()),
			'title',
			10,
		);

		$this->validator->expects(self::once())->method('validate')->with($dto);
		$this->repository->expects(self::once())
			->method('getById')
			->with($this->uuidGenerator->toString($dto->getId()))
			->willReturn($model);

		$handler = new GetCardHandler(
			$this->validator,
			$this->repository,
			$this->uuidGenerator
		);
		$response = $handler($query);

		self::assertEquals([
			'id' => $this->uuidGenerator->toString($dto->getId()),
			'title' => 'title',
			'power' => 10
		], $response->getPayload());
	}

	/**
	 * @param Uuid $uuid
	 * @return CardIdDTO
	 */
	private function createCardDTO(Uuid $uuid): CardIdDTO
	{
		$dto = new CardIdDTO();
		$dto->setId($uuid);

		return $dto;
	}
}
