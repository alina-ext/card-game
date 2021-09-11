<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Generator;

use Symfony\Component\Uid\Uuid;

interface GeneratorInterface
{
	public function generate(): Uuid;

	/**
	 * @param Uuid $id
	 * @return string
	 */
	public function toString(Uuid $id): string;

	/**
	 * @param string $id
	 * @return Uuid
	 */
	public function fromString(string $id): Uuid;
}