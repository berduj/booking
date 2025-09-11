<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Contact;
use App\Entity\ModaliteContact;
use App\Entity\Personne;
use App\Entity\Structure;
use App\Form\Type\CustomDateTimeType;
use App\Form\Type\RemoveFileType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ?Personne $mainPersonne */
        $mainPersonne = $builder->getData()->mainPersonne;

        /** @var ?Structure $mainStructure */
        $mainStructure = $builder->getData()->mainStructure;

        $builder
            ->add('auteur', EntityType::class, [
                'label' => 'Auteur',
                'class' => Personne::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.enabled = true')
                        ->orderBy('p.nom', 'ASC')
                        ->orderBy('p.prenom', 'ASC');
                },
                'required' => true,
            ])
            ->add('date', CustomDateTimeType::class, ['label' => 'Date', 'required' => true])
            ->add('modaliteContact', EntityType::class, [
                'label' => 'Modalité de contact',
                'class' => ModaliteContact::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->andWhere('m.enabled = true')
                        ->orderBy('m.sortable', 'ASC');
                },
                'required' => true,
            ])
            ->add('titre', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('compteRendu', TextareaType::class, [
                'label' => 'Compte rendu',
                'required' => false,
                'attr' => [
                    'rows' => 15,
                ],
            ]);

        if ($mainPersonne !== null && $mainPersonne->getStructures()->count() > 0) {
            $builder->add('structures', EntityType::class, [
                'label' => 'Structures',
                'class' => Structure::class,
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'choices' => $mainPersonne->getStructures(),
            ]);
        }

        if ($mainStructure !== null && $mainStructure->getPersonnes()->count() > 0) {
            $builder->add('personnes', EntityType::class, [
                'label' => 'Personnes',
                'class' => Personne::class,
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
                'attr' => ['class' => 'select-multiple-bordered select-multiple-3cols'],
                'choices' => $mainStructure->getPersonnes(),
            ]);
        }

        $builder
            ->add('autresInterlocuteurs', TextareaType::class, ['label' => 'Autres interlocuteurs', 'required' => false])
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false,
                'help' => 'format : pdf, png, jpg max 4Mo',
                'mapped' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4m',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'maxSizeMessage' => 'Le fichier est trop gros ({{ size }} {{ suffix }}). Le maximum autorisé est {{ limit }} {{ suffix }}.',
                        'mimeTypesMessage' => 'Format invalide, vous devez choisir une image PNG ou JPG ou un fichier PDF',
                    ]),
                ],
            ])
            ->add('removeFile', RemoveFileType::class);

        if (!$builder->getData()->getFilename()) {
            $builder->remove('removeFile');
        }

        if (!$this->security->isGranted('ROLE_CONTACT_EDIT', $builder->getData())) {
            $builder->remove('auteur');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
