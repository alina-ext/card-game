<?php
declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Command\DeleteCardCommand;
use App\Domain\Card\Command\UpdateCardCommand;
use App\Domain\Card\Query\GetCardListQuery;
use App\Domain\Card\Query\GetCardQuery;
use App\Infrastructure\Card\Validator\CardGetDTO;
use App\Infrastructure\Card\Validator\CardUpdateDTO;
use App\Infrastructure\Card\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Card\Validator\CardAddDTO;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class CardController extends AbstractController
{
	private GeneratorInterface $uuidGenerator;
	private ValidatorInterface $validator;
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
		$this->validator = $validator;
		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
	}

	public function add(Request $request): JsonResponse
	{
		$title = $request->get('title');
		$power = $request->get('power');
		$this->validator->validate(new CardAddDTO(
			$title,
			$power
		));

		$command = new AddCardCommand($this->uuidGenerator->generate(), $title, $power);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			null,
			['Location' => $this->generateUrl('catalog_card_get', ['card_id' => $command->getId()->jsonSerialize()])]
		);
	}

	public function getItem(Request $request): JsonResponse
	{
		$id = $this->uuidGenerator->fromString($request->get('card_id'));
		$this->validator->validate(new CardGetDTO($id));

		$query = new GetCardQuery($id);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			$response,
		);
	}

	public function update(Request $request): JsonResponse
	{
		$id = $this->uuidGenerator->fromString($request->get('card_id'));
		$title = $request->get('title');
		$power = $request->get('power');
		$this->validator->validate(new CardUpdateDTO(
			$id,
			$title,
			$power
		));

		$command = new UpdateCardCommand($id, $title, $power);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}

	public function delete(Request $request): JsonResponse
	{
		$id = $this->uuidGenerator->fromString($request->get('card_id'));
		$this->validator->validate(new CardGetDTO($id));

		$command = new DeleteCardCommand($id);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}

	public function list(Request $request): JsonResponse
	{
		$page = $request->get('page_id', 1);
		if (!is_numeric($page) || $page != intval($page) || $page <= 0) {
			return ResponseJson::render(Response::HTTP_BAD_REQUEST, 'Invalid page');
		}

		$query = new GetCardListQuery(intval($page));
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			$response,
		);
	}
}