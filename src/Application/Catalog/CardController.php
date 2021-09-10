<?php
declare(strict_types=1);

namespace App\Application\Catalog;

use App\Application\ApiController;
use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Command\DeleteCardCommand;
use App\Domain\Card\Command\UpdateCardCommand;
use App\Domain\Card\Query\GetCardListQuery;
use App\Domain\Card\Query\GetCardQuery;
use App\Infrastructure\Card\CardEditForm;
use App\Infrastructure\Card\CardIdForm;
use App\Infrastructure\Card\FilterForm;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\Card\CardForm;
use App\Infrastructure\ResponseJson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class CardController extends ApiController
{
	private GeneratorInterface $uuidGenerator;
	private QueryBus $queryBus;
	private CommandBus $commandBus;

	public function __construct(
		GeneratorInterface $uuidGenerator,
		ValidatorInterface $validator,
		QueryBus $queryBus,
		CommandBus $commandBus
	)
	{
		$this->uuidGenerator = $uuidGenerator;
		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
		parent::__construct($validator);
	}

	public function add(Request $request): JsonResponse
	{
		parse_str($request->getContent(), $data);
		$data['id'] = $this->uuidGenerator->toString($this->uuidGenerator->generate());

		$dto = $this->buildObject($data, CardForm::class);
		$command = new AddCardCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			null,
			['Location' => $this->generateUrl('catalog_card_get', [
				'card_id' => $this->uuidGenerator->toString($dto->getId())
			])]
		);
	}

	public function getItem(Request $request): JsonResponse
	{
		$dto = $this->buildObject([
			'id' => $request->get('card_id'),
		], CardIdForm::class);
		$query = new GetCardQuery($dto);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			$response,
		);
	}

	public function update(Request $request): JsonResponse
	{
		parse_str($request->getContent(), $data);
		$data['id'] = $request->get('card_id');
		$dto = $this->buildObject($data, CardEditForm::class);

		$command = new UpdateCardCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}

	public function delete(Request $request): JsonResponse
	{
		$dto = $this->buildObject([
			'id' => $request->get('card_id'),
		], CardIdForm::class);

		$command = new DeleteCardCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}

	public function list(Request $request): JsonResponse
	{
		$dto = $this->buildObject([
			'page_id' => $request->get('page_id'),
		], FilterForm::class);

		$query = new GetCardListQuery($dto);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			$response,
		);
	}
}