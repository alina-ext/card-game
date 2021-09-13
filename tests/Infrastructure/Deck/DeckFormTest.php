<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Deck;

use App\Domain\Deck\DeckAddDTO;
use App\Infrastructure\Common\Generator\UuidGenerator;
use App\Infrastructure\Deck\DeckForm;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Uid\Uuid;

class DeckFormTest extends TypeTestCase
{
	private UuidGenerator $uuidGenerator;

	public function testBuildForm()
	{
		$formData = $this->createDeckDTO();
		$view = $this->factory
			->create(DeckForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 2);
		$this->assertArrayHasKey('id', $view);
		$this->assertArrayHasKey('user_id', $view);

		$this->assertEquals($formData, $dataForm);
	}

	private function createDeckDTO(): DeckAddDTO
	{
		$dto = new DeckAddDTO();
		$dto->setId($this->generateUUID());
		$dto->setUserId($this->generateUUID());

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
