<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Card;

use App\Domain\Card\CardIdDTO;
use App\Infrastructure\Card\CardIdForm;
use App\Infrastructure\Common\Generator\UuidGenerator;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class CardIdFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createCardDTO();

		$view = $this->factory
			->create(CardIdForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 1);
		$this->assertArrayHasKey('id', $view);

		$this->assertEquals($formData, $dataForm);
	}

	public function createCardDTO(): CardIdDTO
	{
		$dto = new CardIdDTO();
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
