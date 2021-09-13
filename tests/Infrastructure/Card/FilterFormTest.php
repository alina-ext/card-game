<?php

namespace App\Tests\Infrastructure\Card;

use App\Infrastructure\Card\FilterDTO;
use App\Infrastructure\Card\FilterForm;
use Symfony\Component\Form\Test\TypeTestCase;

class FilterFormTest extends TypeTestCase
{
	public function testBuildForm()
	{
		$formData = new FilterDTO();
		$formData->setPageId(2);

		$view = $this->factory
			->create(FilterForm::class, $formData)
			->createView();

		$dataForm = $view->vars['value'];

		$this->assertEquals($view->count(), 1);
		$this->assertArrayHasKey('page_id', $view);

		$this->assertEquals($formData, $dataForm);
	}
}
