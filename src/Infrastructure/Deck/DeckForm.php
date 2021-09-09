<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck;

use App\Domain\Deck\DeckAddDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckForm extends AbstractType
{
	final public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('id', UuidType::class)
			->add('user_id', UuidType::class);
	}

	final public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => DeckAddDTO::class
		]);
	}
}