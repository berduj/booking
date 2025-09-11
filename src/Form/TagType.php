<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Tag;
use App\Form\Type\OuiNonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de tag',
                'required' => true,
                'choices' => array_combine(Tag::TYPES, Tag::TYPES),
            ])
            ->add('libelle', TextType::class, ['label' => 'Libellé', 'required' => true])
            ->add('enabled', OuiNonType::class, ['label' => 'Activé', 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
