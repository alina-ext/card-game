<?php
declare(strict_types=1);

namespace App\Tests\Domain\Card\Query;

use App\Domain\Card\Card;
use App\Domain\Card\CardCollection;
use App\Domain\Card\CardRepository;
use App\Domain\Card\PaginationService;
use App\Domain\Card\Query\GetCardListHandler;
use App\Domain\Card\Query\GetCardListQuery;
use App\Infrastructure\Card\FilterDTO;
use App\Infrastructure\Validator;
use App\Infrastructure\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class GetCardListHandlerTest extends TestCase
{
	private CardRepository $repository;
	private ValidatorInterface $validator;
	private PaginationService $paginationService;

	public function setUp(): void
	{
		parent::setUp();
		$this->repository = $this->createMock(CardRepository::class);
		$this->validator = $this->createMock(Validator::class);
		$this->paginationService = $this->createMock(PaginationService::class);
	}

	public function testHandle()
	{
		$query = new GetCardListQuery($this->createFilterDTO(1));
		$dto = $query->getDto();

		$this->validator
			->expects(self::once())
			->method('validate')
			->with($dto);

		$collection = new CardCollection([
			Card::buildCard(
				'id1',
				'title1',
				10,
			),
			Card::buildCard(
				'id2',
				'title2',
				10,
			),
			Card::buildCard(
				'id3',
				'title3',
				10,
			),
			Card::buildCard(
				'id4',
				'title4',
				10,
			)
		], 40);

		$this->repository
			->expects(self::once())
			->method('getList')
			->willReturn($collection);

		$this->paginationService
			->expects(self::once())
			->method('fillResponse');

		$handler = new GetCardListHandler(
			$this->validator,
			$this->repository,
			$this->paginationService
		);

		$handler($query);
	}

	/**
	 * @param int $page
	 * @return FilterDTO
	 */
	private function createFilterDTO(int $page): FilterDTO
	{
		$dto = new FilterDTO();
		$dto->setPageId($page);

		return $dto;
	}
}
