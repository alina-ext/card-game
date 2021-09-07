<?php

namespace App\Application\Catalog;

use App\Domain\Card\Command\AddCardCommand;
use App\Domain\Card\Exceptions\NewCardException;
use App\Domain\Card\Exceptions\ValidationException;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Infrastructure\Card\CardInputDTO;
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

	public function add(Request $request) {
		$title = $request->get('title');
		$power = $request->get('power');
		$errors = $this->validator->validate(
			new CardInputDTO(
				0,
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
			throw new ValidationException($message, Response::HTTP_BAD_REQUEST);
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
}