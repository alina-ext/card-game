<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Response;
use App\Infrastructure\Common\Query\QueryHandler;

class GetCardHandler implements QueryHandler
{
	private CardRepositoryInterface $repository;

	public function __construct(
		CardRepositoryInterface $repository,
	) {
		$this->repository = $repository;
	}

	public function __invoke(GetCardQuery $query): Response
	{
		$dbModel = $this->repository->getById($query->getId());
		$model = new Card($dbModel->getId(), $dbModel->getTitle(), $dbModel->getPower());
		$response = new Response();
		$model->fillResponse($response);

		return $response;
	}
}