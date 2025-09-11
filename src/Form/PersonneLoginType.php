<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DepartementDomaine;
use App\Entity\Personne;
use App\Entity\Profil;
use App\Entity\Service;
use App\Form\Type\OuiNonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PersonneLoginType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Identifiant',
                'required' => false,
                'attr' => ['autocomplete' => 'off', 'data-lpignore' => 'true'],
            ])
            ->add('profil', EntityType::class, [
                'class' => Profil::class,
                'label' => 'Profil',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'first_options' => ['label' => 'Mot de passe', 'hash_property_path' => 'password'],
                'second_options' => ['label' => 'Répétez le mot de passe'],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'data-lpignore' => 'true'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
