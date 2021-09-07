<?php

namespace App\Infrastructure;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiException
{
	public function onKernelException(ExceptionEvent $event): void
	{
		$e = $event->getThrowable();

		if ($e instanceof Exception) {
			$event->setResponse(ResponseJson::render(Response::HTTP_BAD_REQUEST, $e->getMessage()));

			return;
		}
	}
}
