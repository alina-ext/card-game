<?php

namespace App\Tests\Infrastructure\Deck\Card;

use App\Domain\Deck\Card\DeckCardDTO;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Deck\Card\DeckCardForm;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class DeckCardFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createDeckDTO();
		$view = $this->factory
			->create(DeckCardForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 3);
		$this->assertArrayHasKey('deck_id', $view);
		$this->assertArrayHasKey('card_id', $view);
		$this->assertArrayHasKey('amount', $view);

		$this->assertEquals($formData, $dataForm);
	}

	private function createDeckDTO(): DeckCardDTO
	{
		$dto = new DeckCardDTO();
		$dto->setDeckId($this->generateUUID());
		$dto->setCardId($this->generateUUID());
		$dto->setAmount(rand(0, 10));

		return $dto;
	}

	private function generateUUID(): Uuid
	{
		if (!isset($this->uuidGenerator)) {
			$this->uuidGenerator = new UuidGenerator();
		}

		return $this->uuidGenerator->generate();
	}
}
