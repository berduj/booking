<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Profil;
use App\Security\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Permissions',
                'multiple' => true,
                'expanded' => true,
                'choices' => array_flip(Role::ROLES),
                'attr' => ['class' => ' bordered select-multiple-bordered'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
