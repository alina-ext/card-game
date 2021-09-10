<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\CardIdDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardIdForm extends AbstractType
{
	final public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('id', UuidType::class);
	}

	final public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => CardIdDTO::class
		]);
	}
}