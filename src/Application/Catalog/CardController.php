<?php
declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Command\DeleteCardCommand;
use App\Domain\Card\Command\UpdateCardCommand;
use App\Domain\Card\Query\GetCardQuery;
use App\Infrastructure\Card\CardGetInputDTO;
use App\Infrastructure\Card\CardUpdateInputDTO;
use App\Infrastructure\Card\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Card\CardAddInputDTO;
use App\Infrastructure\Common\Generator\GeneratorInterface;
//use Psr\Log\LoggerInterface;

class CardController extends AbstractController
{
//	private LoggerInterface $logger;
	private GeneratorInterface $uuidGenerator;
	private ValidatorInterface $validator;
	private QueryBus $queryBus;
	private CommandBus $commandBus;

	public function __construct(/*LoggerInterface $logger, */
		GeneratorInterface $uuidGenerator,
		ValidatorInterface $validator,
		QueryBus $queryBus,
		CommandBus $commandBus
	)
	{
//		$this->logger = $logger;
		$this->uuidGenerator = $uuidGenerator;
		$this->validator = $validator;
		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
	}

	public function add(Request $request): JsonResponse {
		$title = $request->get('title');
		$power = $request->get('power');
		$this->validator->validate(new CardAddInputDTO(
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
		$this->validator->validate(new CardGetInputDTO($id));

		$query = new GetCardQuery($id);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			$response,
		);
	}

	public function update(Request $request): JsonResponse {
		$id = $this->uuidGenerator->fromString($request->get('card_id'));
		$title = $request->get('title');
		$power = $request->get('power');
		$this->validator->validate(new CardUpdateInputDTO(
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

	public function delete(Request $request): JsonResponse {
		$id = $this->uuidGenerator->fromString($request->get('card_id'));
		$this->validator->validate(new CardGetInputDTO($id));

		$command = new DeleteCardCommand($id);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_OK,
			'',
			null
		);
	}
}