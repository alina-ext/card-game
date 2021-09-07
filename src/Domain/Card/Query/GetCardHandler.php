<?php

namespace App\Domain\Card\Query;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Response;
use App\Infrastructure\Common\Query\QueryHandler;
use App\Infrastructure\Card\ValidatorInterface;

class GetCardHandler implements QueryHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
	) {
		$this->validator = $validator;
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