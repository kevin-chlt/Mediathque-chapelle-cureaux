<?php

namespace App\Form;

use App\Entity\Authors;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class AuthorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'auteur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'ERREUR AJOUT AUTEUR: Vous devez remplir ce champ.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'ERREUR AJOUT AUTEUR: Le titre doit contenir au maximum {{ limit }} caractères.',
                    ]),
                    new Type([
                        'type' => 'alpha',
                        'message' => 'ERREUR AJOUT AUTEUR: Veuillez utiliser seulement des lettres.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Authors::class,
        ]);
    }
}
