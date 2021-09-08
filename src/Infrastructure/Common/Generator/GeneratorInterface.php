<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Generator;

use Symfony\Component\Uid\Uuid;

interface GeneratorInterface
{
	public function generate(): Uuid;

	public function toString(Uuid $id): string;

	public function fromString(string $id): Uuid;
}