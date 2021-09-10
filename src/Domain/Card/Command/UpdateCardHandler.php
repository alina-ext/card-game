<?php
declare(strict_types=1);

namespace App\Domain\Card\Command;

use App\Domain\Card\CardRepositoryInterface;
use App\Infrastructure\Common\Command\CommandHandler;
use App\Infrastructure\ValidatorInterface;
use App\Infrastructure\Common\Generator\GeneratorInterface;

class UpdateCardHandler implements CommandHandler
{
	private CardRepositoryInterface $repository;
	private ValidatorInterface $validator;
	private GeneratorInterface $uuidGenerator;

	public function __construct(
		ValidatorInterface $validator,
		CardRepositoryInterface $repository,
		GeneratorInterface $uuidGenerator
	)
	{
		$this->validator = $validator;
		$this->repository = $repository;
		$this->uuidGenerator = $uuidGenerator;
	}

	public function __invoke(UpdateCardCommand $command): void
	{
		$dto = $command->getDto();
		$this->validator->validate($dto);

		$model = $this->repository->getById(
			$this->uuidGenerator->toString($dto->getId())
		);
		if (null !== ($title = $dto->getTitle())) {
			$model->setTitle($title);
			$model->pushEvent('card:update:title');
		}
		if (null !== ($power = $dto->getPower())) {
			$model->setPower(intval($power));
			$model->pushEvent('card:update:power');
		}

		$this->repository->save($model);
	}

	public static function getHandledMessages(): iterable
	{
		yield UpdateCardCommand::class => [
			'method' => '__invoke'
		];
	}
}