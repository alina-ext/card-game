<?php

namespace App\Infrastructure;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseJson
{
	public static function render(int $code, ?string $message, Payload $payload = null, ?array $header = []): JsonResponse
	{
		$info = [];
		if ($message) {
			$info['message'] = $message;
		}
		if ($payload) {
			$info['data'] = $payload->getPayload();
		}

		return new JsonResponse($info ? $info : null, $code, $header);
	}
}
