<?php
declare(strict_types=1);

namespace App\Domain\Card\Query;

use App\Infrastructure\Common\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetCardQuery implements Query
{
	private Uuid $id;

	public function __construct(Uuid $id)
	{
		$this->id = $id;
	}

	public function getId(): Uuid
	{
		return $this->id;
	}
}