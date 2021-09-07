<?php

namespace App\Application\Catalog;

use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Exceptions\ValidationException;
use App\Domain\Card\Query\GetCardQuery;
use App\Infrastructure\Card\CardGetDTO;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Infrastructure\Card\CardAddDTO;
use Psr\Log\LoggerInterface;

class CardController extends AbstractController
{
	private LoggerInterface $logger;
	private ValidatorInterface $validator;
	private QueryBus $queryBus;
	private CommandBus $commandBus;

	public function __construct(LoggerInterface $logger, ValidatorInterface $validator, QueryBus $queryBus, CommandBus $commandBus)
	{
		$this->logger = $logger;
		$this->validator = $validator;
		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
	}

	public function add(Request $request): JsonResponse {
		$title = $request->get('title');
		$power = $request->get('power');
		$errors = $this->validator->validate(
			new CardAddDTO(
				$title,
				$power
			)
		);
		if (($count = $errors->count())) {
			$messages = [];
			for ($i=0; $i<$count; $i++) {
				$messages[] = $errors->get($i)->getMessage();
			}
			$message = implode("\n", $messages);
			$this->logger->info($message);
			throw new ValidationException($message);
		}

		$command = new AddCardCommand(Uuid::v4(), $title, $power);
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
		$id = $request->get('card_id');
		$errors = $this->validator->validate(
			new CardGetDTO($id)
		);
		if (($count = $errors->count())) {
			$messages = [];
			for ($i=0; $i<$count; $i++) {
				$messages[] = $errors->get($i)->getMessage();
			}
			$message = implode("\n", $messages);
			$this->logger->info($message);
			throw new ValidationException($message);
		}

		$query = new GetCardQuery($id);
		$response = $this->queryBus->handle($query);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			$response,
		);
	}
}