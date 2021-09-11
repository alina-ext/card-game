<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Card\Exceptions\BadCommand;
use App\Domain\Card\Exceptions\ConflictException;
use App\Domain\Card\Exceptions\DBException;
use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Card\Exceptions\ValidationException AS CardValidationException;
use App\Domain\Deck\Exceptions\DBException AS DeckDBException;
use App\Domain\Deck\Exceptions\DeckSizeLimitReachedException;
use App\Domain\Deck\Exceptions\NotEnoughCards;
use App\Domain\Deck\Exceptions\NotFoundException AS DeckNotFoundException;
use App\Domain\Deck\Exceptions\ValidationException AS DeckValidationException;
use App\Infrastructure\Common\Generator\GeneratorException;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
	public function onKernelException(ExceptionEvent $event): void
	{
		$e = $event->getThrowable();

		if ($e instanceof HandlerFailedException) {
			$e = $e->getPrevious();
		}
		if ($e instanceof ValidationException ||
			$e instanceof CardValidationException ||
			$e instanceof ConflictException ||
			$e instanceof DeckSizeLimitReachedException ||
			$e instanceof NotEnoughCards ||
			$e instanceof DeckValidationException
		) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_REQUEST, $e->getMessage()));

			return;
		}
		if ($e instanceof NotFoundException || $e instanceof DeckNotFoundException) {
			$event->setResponse(ResponseJson::render(Response::HTTP_NOT_FOUND, $e->getMessage()));

			return;
		}
		if ($e instanceof DBException ||
			$e instanceof DeckDBException ||
			$e instanceof BadCommand ||
			$e instanceof GeneratorException ||
			$e instanceof Exception) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_GATEWAY, $e->getMessage()));

			return;
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::EXCEPTION => 'onKernelException',
		];
	}
}
