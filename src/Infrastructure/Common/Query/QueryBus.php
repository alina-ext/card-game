<?php

namespace App\Infrastructure\Common\Query;

interface QueryBus
{
	public function handle(Query $query): mixed;
}
