<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Export;
use App\Entity\Profil;
use App\Form\Type\OuiNonType;
use App\Security\Voter\ExportVoter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'Libellé', 'required' => true])
            ->add(
                'requete',
                TextareaType::class,
                [
                    'label' => 'Requête SQL',
                    'required' => true,
                    'attr' => [
                        'class' => 'code'],
                ],
            )
            ->add('profils', EntityType::class, [
                'class' => Profil::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'select-multiple-3cols bordered select-multiple-bordered'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.sortable', 'ASC');
                },
            ])
            ->add('enabled', OuiNonType::class, ['label' => 'Activé', 'required' => true]);

        if (!$this->security->isGranted(ExportVoter::EXECUTE, $builder->getData())) {
            $builder->remove('injectUser')
                ->remove('requete');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Export::class,
        ]);
    }
}
