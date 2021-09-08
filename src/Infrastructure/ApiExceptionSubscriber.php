<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Card\Exceptions\ConflictException;
use App\Domain\Card\Exceptions\DBException;
use App\Domain\Card\Exceptions\NotFoundException;
use App\Domain\Card\Exceptions\ValidationException;
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
		if ($e instanceof ValidationException || $e instanceof ConflictException) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_REQUEST, $e->getMessage()));

			return;
		}
		if ($e instanceof NotFoundException) {
			$event->setResponse(ResponseJson::render(Response::HTTP_NOT_FOUND, $e->getMessage()));

			return;
		}
		if ($e instanceof DBException) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_GATEWAY, $e->getMessage()));

			return;
		}
		if ($e instanceof Exception) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_REQUEST, $e->getMessage()));

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
