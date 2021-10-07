<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Type;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new notBlank([
                        'message' => 'Vous devez remplir ce champ.',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre email doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre email doit contenir au moins {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
        ->add('lastname', TextType::class, [
            'label' => 'Prenom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Vous devez remplir ce champ.'
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                    'max' => 255,
                    'maxMessage' => 'Votre prénom doit contenir au maximum {{ limit }} caractères.',
                ]),
                new Type([
                    'type' => 'alpha',
                    'message' => 'Votre prénom doit être composé uniquement de lettre.',
                ])
            ]
        ])
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre nom doit contenir au maximum {{ limit }} caractères.',
                    ]),
                    new Type([
                        'type' => 'alpha',
                        'message' => 'Votre nom doit être composé uniquement de lettre.',
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre adresse doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre adresse doit contenir au maximum {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget'=> 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ]),
                    new GreaterThan([
                        'value' => '-120 years',
                        'message' => 'Date de naissance incorrect.'
                    ]),
                    new LessThan([
                        'value' => '+5 years',
                        'message' => 'Date de naissance incorrect.'
                    ])
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
