<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PhotoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => 'Photo',
                'help' => 'format : png, jpg max 4Mo',
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new File([
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
            ]
        );
    }

    public function getParent(): string
    {
        return FileType::class;
    }
}
