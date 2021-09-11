<?php
declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class ApiController extends AbstractController
{
	private ValidatorInterface $validator;

	public function __construct(
		ValidatorInterface $validator,
	)
	{
		$this->validator = $validator;
	}

	protected function buildObject(array $data, $nameClassType)
	{
		$buildForm = $this->createForm($nameClassType);
		$buildForm->submit($data);
		$this->validator->validate($buildForm);

		return $buildForm->getData();
	}
}