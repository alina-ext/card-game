<?php
declare(strict_types=1);

namespace App\Application\Deck;

use App\Application\ApiController;
use App\Domain\Deck\Command\AddDeckCommand;
use App\Domain\Deck\Command\AddDeckCardCommand;
use App\Domain\Deck\Command\DeleteDeckCardCommand;
use App\Domain\Deck\Query\GetDeckQuery;
use App\Infrastructure\Deck\Card\DeckCardForm;
use App\Infrastructure\Deck\DeckForm;
use App\Infrastructure\Deck\DeckIdForm;
use App\Infrastructure\Deck\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeckController extends ApiController
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

		$dto = $this->buildObject($data, DeckForm::class);
		$command = new AddDeckCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			null,
			['Location' => $this->generateUrl('deck_get', [
				'deck_id' => $this->uuidGenerator->toString($dto->getId())
			])]
		);
	}

	public function getItems(Request $request): JsonResponse
	{
		$dto = $this->buildObject(['id' => $request->get('deck_id')], DeckIdForm::class);
		$query = new GetDeckQuery($dto);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			$response
		);
	}

	public function addItem(Request $request): JsonResponse
	{
		parse_str($request->getContent(), $data);
		$data['deck_id'] = $request->get('deck_id');

		$dto = $this->buildObject($data, DeckCardForm::class);

		$command = new AddDeckCardCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}

	public function deleteItem(Request $request): JsonResponse
	{
		parse_str($request->getContent(), $data);
		$data['deck_id'] = $request->get('deck_id');

		$dto = $this->buildObject($data, DeckCardForm::class);

		$command = new DeleteDeckCardCommand($dto);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			null
		);
	}
}