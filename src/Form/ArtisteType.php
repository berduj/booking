<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Artiste;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom', 'required' => true])
            ->add('codePostal', TextType::class, ['label' => 'Code postal', 'required' => false])
            ->add('commune', TextType::class, ['label' => 'Commune', 'required' => false])
            ->add('tarif', NumberType::class, ['label' => 'Tarif indicatif', 'required' => false])
            ->add('commentaireTarif', TextareaType::class, ['label' => 'Commentaire tarif', 'required' => false])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.type = :type')
                        ->setParameter('type', Tag::TYPE_ARTISTE)
                        ->andWhere('t.enabled = true')
                        ->orderBy('t.sortable', 'ASC');
                },
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'small'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
        ]);
    }
}
