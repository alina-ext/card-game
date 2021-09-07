<?php

namespace App\Infrastructure\Card;

use App\Domain\Card\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Infrastructure\Card\ValidatorInterface AS VI;

class Validator implements VI
{
	private ValidatorInterface $validator;
	private LoggerInterface $logger;

	public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
	{
		$this->validator = $validator;
		$this->logger = $logger;
	}

	public function validate(CardDTOInterface $DTO) {
		$errors = $this->validator->validate($DTO);
		if (($count = $errors->count())) {
			$messages = [];
			for ($i=0; $i<$count; $i++) {
				$messages[] = $errors->get($i)->getMessage();
			}
			$message = implode(", ", $messages);
			$this->logger->info($message);
			throw new ValidationException($message);
		}
	}
}