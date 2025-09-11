<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Alerte;
use App\Form\Type\CustomDateType;
use App\Form\Type\OuiNonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlerteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre', 'required' => true])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
            ->add('date', CustomDateType::class, ['label' => 'Alerte active à partir du ', 'required' => true])
            ->add('enabled', OuiNonType::class, ['label' => 'Alerte active', 'required' => true])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alerte::class,
        ]);
    }
}
