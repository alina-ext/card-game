<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\PaginationService;
use App\Domain\Card\ResponseCatalog;
use App\Infrastructure\Card\FilterService;
use App\Infrastructure\Common\Query\QueryHandler;

class GetCardListHandler implements QueryHandler
{
	private CardRepositoryInterface $repository;
	private PaginationService $paginationService;

	public function __construct(
		CardRepositoryInterface $repository,
		PaginationService $paginationService
	)
	{
		$this->repository = $repository;
		$this->paginationService = $paginationService;
	}

	public function __invoke(GetCardListQuery $query): ResponseCatalog
	{
		$filter = new FilterService($query->getPage());
		$collection = $this->repository->getList($filter);

		$response = new ResponseCatalog();
		$this->paginationService->fillResponse($response, $filter, $collection);

		return $response;
	}
}