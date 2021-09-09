<?php
declare(strict_types=1);

namespace App\Domain\Deck\Query;

use App\Domain\Deck\DeckRepositoryInterface;
use App\Domain\Deck\Response;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Query\QueryHandler;
use App\Infrastructure\ValidatorInterface;

class GetDeckHandler implements QueryHandler
{
	private DeckRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		DeckRepositoryInterface $repository,
		GeneratorInterface $uuidGenerator
	)
	{
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(GetDeckQuery $query): Response
	{
		$dto = $query->getDto();
		$this->validator->validate($dto);

		$model = $this->repository->getById($this->uuidGenerator->toString($dto->getId()));
		$response = new Response();
		$model->fillResponse($response);

		return $response;
	}
}