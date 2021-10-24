<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
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
                'label' => 'Adresse e-mail',
                'disabled' => $options['is_edit'],
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$options['is_edit'],
                'invalid_message' => 'Les 2 mots de passe doivent être identiques',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['autocomplete' => 'new-password'],
                    'required' => !$options['is_edit'],
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                            'max' => 255,
                            'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'required' => !$options['is_edit'],
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                            'max' => 255,
                            'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.',
                        ]),
                    ],
                ]
            ])
        ->add('lastname', TextType::class, [
            'label' => 'Prénom',
            'disabled' => $options['is_edit'],
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
                    'message' => 'Votre prénom doit être composé uniquement de lettres et sans caractères spécial.',
                ])
            ]
        ])
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
                'disabled' => $options['is_edit'],
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
                        'message' => 'Votre nom doit être composé uniquement de lettres et sans caractères spécial.',
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
                'disabled' => $options['is_edit'],
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
                        'value' => '-6 years',
                        'message' => 'Vous devez avoir 6 ans minimum pour adhérer à la médiathèque.'
                    ])
                ]
            ])
        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $events){
            $form = $events->getForm();
            $user = $events->getData();

            if (!empty($user->getEmail())) {
                $form->add('oldPassword', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'Mot de passe',
                    'error_bubbling' => true,
                    'constraints' => [
                        new UserPassword([
                            'message' => 'Mot de passe invalide.'
                        ])
                    ]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'is_edit' => false,
        ]);
    }
}
