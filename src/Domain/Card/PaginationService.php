<?php
declare(strict_types=1);

namespace App\Domain\Card;

use App\Infrastructure\Card\FilterService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationService
{
	private UrlGeneratorInterface $router;

	public function __construct(UrlGeneratorInterface $router)
	{
		$this->router = $router;
	}

	public function fillResponse(ResponseCatalog $response, FilterService $filter, CardCollection $collection): void
	{
		$page = $filter->getPage();
		$totalPages = intval(ceil($collection->getTotal() / $filter->getLimit()));

		$response->setList($collection->getItems());

		$prev = min($totalPages, $page - 1);
		$preUrl = $this->generatePrevious($prev);
		$response->setPrev($preUrl);

		$nextUrl = $this->generateNext($totalPages, $page);
		$response->setNext($nextUrl);
	}

	private function generatePrevious(int $prev): ?string
	{
		return $prev >= 1 ? $this->router->generate('catalog_card_list', ['page_id' => $prev]) : null;
	}

	private function generateNext(int $totalPages, int $page): ?string
	{
		return $totalPages > $page ? $this->router->generate('catalog_card_list', ['page_id' => $page + 1]) : null;
	}
}