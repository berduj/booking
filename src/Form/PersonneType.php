<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DepartementDomaine;
use App\Entity\Personne;
use App\Entity\Profil;
use App\Entity\Service;
use App\Entity\Tag;
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

class PersonneType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom', 'required' => true])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'required' => false])
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => ['Monsieur' => 'Monsieur', 'Madame' => 'Madame'],
                'required' => false,
            ]);

        if ($this->security->isGranted('ROLE_VIP_EDIT')) {
            $builder->add('vip', OuiNonType::class, ['label' => 'VIP', 'required' => true]);
        }

        $builder
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false])
            ->add('telephone_mobile', TelType::class, ['label' => 'Téléphone mobile', 'required' => false])
            ->add('telephone_fixe', TelType::class, ['label' => 'Téléphone fixe', 'required' => false])

            ->add('fonction', TextType::class, ['label' => 'Fonction', 'required' => false])
            ->add('departementDomaine', EntityType::class, [
                'label' => 'Département/Domaine',
                'class' => DepartementDomaine::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.enabled = true')
                        ->orderBy('d.sortable', 'ASC');
                },
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['class' => 'small'],
            ])
            ->add('complementAdresse', TextareaType::class, [
                'label' => 'Complément d\'adresse',
                'required' => false,
                'attr' => ['class' => 'small'],
            ])
            ->add('codePostal', TextType::class, ['label' => 'Code postal', 'required' => false])
            ->add('commune', TextType::class, ['label' => 'Commune', 'required' => false])
            ->add('pays', TextType::class, ['label' => 'Pays', 'required' => false])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.type = :type')
                        ->setParameter('type', Tag::TYPE_PERSONNE)
                        ->andWhere('t.enabled = true')
                        ->orderBy('t.sortable', 'ASC');
                },
            ])
            ->add('enabled', OuiNonType::class, ['label' => 'Activé']);

        if (!$this->security->isGranted('ROLE_USER_EDIT')) {
            $builder
                ->remove('username')
                ->remove('plainPassword')
                ->remove('profil');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
