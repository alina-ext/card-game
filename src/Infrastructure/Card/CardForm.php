<?php
declare(strict_types=1);

namespace App\Infrastructure\Card;

use App\Domain\Card\CardDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardForm extends AbstractType
{
	final public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('id', UuidType::class)
			->add('title', TextType::class)
			->add('power', IntegerType::class);
	}

	final public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => CardDTO::class
		]);
	}
}