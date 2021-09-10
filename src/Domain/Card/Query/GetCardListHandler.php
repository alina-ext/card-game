<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\PaginationService;
use App\Domain\Card\ResponseCatalog;
use App\Infrastructure\Card\FilterService;
use App\Infrastructure\Common\Query\QueryHandler;
use App\Infrastructure\ValidatorInterface;

class GetCardListHandler implements QueryHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private PaginationService $paginationService;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
		PaginationService $paginationService
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->paginationService = $paginationService;
	}

	public function __invoke(GetCardListQuery $query): ResponseCatalog
	{
		$dto = $query->getDto();
		$this->validator->validate($dto);

		$filter = new FilterService($dto->getPageId());
		$collection = $this->repository->getList($filter);

		$response = new ResponseCatalog();
		$this->paginationService->fillResponse($response, $filter, $collection);

		return $response;
	}
}