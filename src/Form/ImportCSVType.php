<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportCSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('import', FileType::class, [
                'label' => 'Importer un fichier CSV',
                'label_attr' => ['style'=> 'color: white'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez remplir le champ.']),
                    new File([
                        'maxSize' => '5096k',
                        'maxSizeMessage' => 'Taille de fichier maximum autorisé: {{ limit }}',
                        'mimeTypes' => [
                            'text/csv'
                        ],
                        'mimeTypesMessage' => 'Fichier .csv uniquement autorisé.',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
