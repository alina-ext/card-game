<?php
declare(strict_types=1);

namespace App\Application\Deck;

use App\Domain\Deck\Command\AddDeckCommand;
use App\Infrastructure\Deck\Validator\DeckAddDTO;
use App\Infrastructure\Deck\ValidatorInterface;
use App\Infrastructure\Common\Command\CommandBus;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Infrastructure\Common\Query\QueryBus;
use App\Infrastructure\ResponseJson;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeckController extends AbstractController
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
		$user = $this->uuidGenerator->fromString($request->get('user_id'));
		$this->validator->validate(new DeckAddDTO(
			$user,
		));

		$command = new AddDeckCommand($this->uuidGenerator->generate(), $user);
		$this->commandBus->dispatch($command);

		return ResponseJson::render(
			Response::HTTP_CREATED,
			'',
			null,
			['Location' => $this->generateUrl('deck_get', ['deck_id' => $this->uuidGenerator->toString($command->getId())])]
		);
	}
}