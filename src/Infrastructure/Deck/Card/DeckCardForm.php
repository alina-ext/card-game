<?php
declare(strict_types=1);

namespace App\Infrastructure\Deck\Card;

use App\Domain\Deck\DeckCardAddDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckCardForm extends AbstractType
{
	final public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('deck_id', UuidType::class)
			->add('card_id', UuidType::class)
			->add('amount', IntegerType::class);
	}

	final public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => DeckCardAddDTO::class
		]);
	}
}