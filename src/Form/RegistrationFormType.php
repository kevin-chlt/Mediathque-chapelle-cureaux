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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'disabled' => $options['is_edit'],
                'invalid_message' => 'L\'email doit être au format abc@abc.ab'
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
                ]
            ])
        ->add('lastname', TextType::class, [
            'label' => 'Prénom',
            'disabled' => $options['is_edit'],
        ])
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
                'disabled' => $options['is_edit'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('birthdate', BirthdayType::class, [
                'disabled' => $options['is_edit'],
                'label' => 'Date de naissance',
                'widget'=> 'single_text',
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
