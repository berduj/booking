<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
                'required' => true,

                'help' => 'format : png, jpg max 4Mo',
                'mapped' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '4m',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'maxSizeMessage' => 'Le fichier est trop gros ({{ size }} {{ suffix }}). Le maximum autorisé est {{ limit }} {{ suffix }}.',
                        'mimeTypesMessage' => 'Format invalide, vous devez choisir une image PNG ou JPG',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
