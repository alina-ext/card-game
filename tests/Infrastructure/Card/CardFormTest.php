<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Card;

use App\Domain\Card\CardDTO;
use App\Infrastructure\Card\CardForm;
use App\Infrastructure\Common\Generator\UuidGenerator;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class CardFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createCardDTO();

		$view = $this->factory
			->create(CardForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 3);
		$this->assertArrayHasKey('id', $view);
		$this->assertArrayHasKey('title', $view);
		$this->assertArrayHasKey('power', $view);

		$this->assertEquals($formData, $dataForm);
	}

	private function createCardDTO(): CardDTO
	{
		$dto = new CardDTO();
		$dto->setId($this->generateUUID());
		$dto->setTitle('testCard');
		$dto->setPower(10);

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
