<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [

                'constraints' => [
                    new notBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre email doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre email doit contenir au moins {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
        ->add('lastname', TextType::class, [
            'label' => 'Prenom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Vous devez remplir ce champ'
                ]),
                new Length([
                    'min' => 2,
                    'max' => 255
                ])
            ]
        ])
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255
                    ])
                ]
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget'=> 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
