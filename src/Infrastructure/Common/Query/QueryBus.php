<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Query;

interface QueryBus
{
	/**
	 * @param Query $query
	 * @return mixed
	 */
	public function handle(Query $query): mixed;
}
