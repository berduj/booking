<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Structure;
use App\Entity\Tag;
use App\Entity\TypeStructure;
use App\Form\Type\CustomDateType;
use App\Form\Type\OuiNonType;
use App\Validator\Siret;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructureType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $siretRequired = true;
        if ($builder->getData()->getId()) {
            $siretRequired = false;
        }

        $builder
            ->add('nom', TextType::class, ['label' => 'Nom usuel', 'required' => true])
            ->add('raisonSociale', TextType::class, ['label' => 'Raison sociale', 'required' => true])
            ->add('typeStructures', EntityType::class, ['label' => 'Type de structure',
                'class' => TypeStructure::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.enabled = true')
                        ->orderBy('u.sortable', 'ASC');
                },
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.type = :type')
                        ->setParameter('type', Tag::TYPE_STRUCTURE)
                        ->andWhere('t.enabled = true')
                        ->orderBy('t.sortable', 'ASC');
                },
            ])
            ->add('siret', TextType::class, [
                'label' => 'Siret',
                'required' => false,
                'constraints' => [
                    new Siret(),
                ],
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
            ->add('telephone', TextType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('email', TextType::class, ['label' => 'Email', 'required' => false])

            ->add('datePremierContact', CustomDateType::class, ['label' => 'Date de premier contact', 'required' => false])
            ->add('infosDiverses', TextareaType::class, ['label' => 'Informations diverses', 'required' => false])
            ->add('adminOnly', OuiNonType::class, ['label' => 'Seuls les administrateurs peuvent modifier cette structure'])
            ->add('enabled', OuiNonType::class, ['label' => 'Activé']);

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $builder->remove('adminOnly');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Structure::class,
        ]);
    }
}
