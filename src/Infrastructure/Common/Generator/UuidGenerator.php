<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Generator;

use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class UuidGenerator implements GeneratorInterface
{
	public function generate(): Uuid
	{
		return Uuid::v4();
	}

	public function toString(Uuid $id): string
	{
		try {
			return $id->jsonSerialize();
		} catch (InvalidArgumentException $e) {
			throw new GeneratorException(sprintf("id %s is not valid", $id));
		}
	}

	public function fromString(string $id): Uuid
	{
		try {
			return Uuid::fromString($id);
		} catch (InvalidArgumentException $e) {
			throw new GeneratorException(sprintf("id %s is not valid", $id));
		}
	}
}