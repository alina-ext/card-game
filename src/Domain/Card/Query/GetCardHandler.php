<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Domain\Card\Card;
use App\Domain\Card\CardRepositoryInterface;
use App\Domain\Card\Response;
use App\Domain\Card\Validator\CardGetDTO;
use App\Infrastructure\Card\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Query\QueryHandler;

class GetCardHandler implements QueryHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
		GeneratorInterface $uuidGenerator
	) {
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(GetCardQuery $query): Response
	{
		$cardDTO = new CardGetDTO(
			$query->getId(),
		);
		$this->validator->validate($cardDTO);

		$modelDB = $this->repository->getById($this->uuidGenerator->toString($query->getId()));
		$model = new Card($modelDB->getId(), $modelDB->getTitle(), $modelDB->getPower());
		$response = new Response();
		$model->fillResponse($response);

		return $response;
	}
}