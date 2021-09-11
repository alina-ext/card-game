<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\CardRepository;
use App\Domain\Card\Response;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Query\QueryHandler;

class GetCardHandler implements QueryHandler
{
	private CardRepository $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepository $repository,
		GeneratorInterface $uuidGenerator
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(GetCardQuery $query): Response
	{
		$dto = $query->getDto();
		$this->validator->validate($dto);

		$model = $this->repository->getById(
			$this->uuidGenerator->toString($dto->getId())
		);

		$response = new Response();
		$model->fillResponse($response);

		return $response;
	}
}