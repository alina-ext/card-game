<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;
use App\Domain\Card\Card;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AddCardHandler implements CommandHandler, MessageHandlerInterface
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

	public function __invoke(AddCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = new Card(
			$this->uuidGenerator->toString($dto->getId()),
			$dto->getTitle(),
			$dto->getPower()
		);
		$model->pushEvent('card:add');

		$this->repository->save($model);
	}

	public static function getHandledMessages(): iterable
	{
		yield AddCardCommand::class => [
			'method' => '__invoke',
			'from_transport' => 'async'
		];
	}
}