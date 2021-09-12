<?php

namespace App\Tests\Infrastructure\Card;

use App\Domain\Card\CardEditDTO;
use App\Infrastructure\Card\CardEditForm;
use App\Infrastructure\Common\Generator\UuidGenerator;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class CardEditFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createCardDTO();

		$view = $this->factory
			->create(CardEditForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertArrayHasKey('id', $view);
		$this->assertArrayHasKey('title', $view);
		$this->assertArrayHasKey('power', $view);

		$this->assertEquals($formData->getId(), $dataForm->getId());
		$this->assertEquals($formData->getTitle(), $dataForm->getTitle());
		$this->assertEquals($formData->getPower(), $dataForm->getPower());
	}

	public function createCardDTO(): CardEditDTO {
		$dto = new CardEditDTO();
		$dto->setId($this->generateUUID());
		$dto->setTitle('testCard');

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
