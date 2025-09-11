<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Type\OuiNonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportSiretExcelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
                'required' => true,

                'help' => 'format : .xls, .xlsx, max 4Mo',
                'mapped' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4m',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ],
                        'maxSizeMessage' => 'Le fichier est trop gros ({{ size }} {{ suffix }}). Le maximum autorisé est {{ limit }} {{ suffix }}.',
                        'mimeTypesMessage' => 'Format invalide, vous devez choisir un fichier xls ou xlsx',
                    ]),
                ],
            ])
            ->add('geocode', OuiNonType::class, [
                'label' => 'Géolocaliser les structures par leur adresse ',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
