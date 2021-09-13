<?php

namespace App\Tests\Infrastructure\Deck;

use App\Domain\Deck\DeckIdDTO;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Deck\DeckIdForm;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class DeckIdFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createDeckDTO();
		$view = $this->factory
			->create(DeckIdForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 1);
		$this->assertArrayHasKey('id', $view);

		$this->assertEquals($formData, $dataForm);
	}

	private function createDeckDTO(): DeckIdDTO
	{
		$dto = new DeckIdDTO();
		$dto->setId($this->generateUUID());

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
