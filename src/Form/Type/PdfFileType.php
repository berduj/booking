<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PdfFileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => 'Fichier',
                'help' => 'format : pdf max 4Mo',
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4m',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'maxSizeMessage' => 'Le fichier est trop gros ({{ size }} {{ suffix }}). Le maximum autorisé est {{ limit }} {{ suffix }}.',
                        'mimeTypesMessage' => 'Format invalide, vous devez choisir une image PDF, PNG ou JPG',
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
