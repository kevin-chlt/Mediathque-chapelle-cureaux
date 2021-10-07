<?php

namespace App\Form;

use App\Entity\Authors;
use App\Entity\Books;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BooksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('description',TextareaType::class,[
                'label' => 'Synopsis',
                'attr' => ['class'=> 'materialize-textarea'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ]),
                ]
            ])
            ->add('parutedAt', DateType::class, [
                'label' => 'Date de parution',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ])
                ]
            ])
            ->add('isFree', ChoiceType::class, [
                'label' => 'Disponible ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ])
                ]
            ])
            ->add('cover', FileType::class, [
                'label' => 'Couverture',
                'required' => false,
                'label_attr' => ['style' => 'color: #FFFFFF']
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'multiple'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ])
                ]
            ])
            ->add('authors', EntityType::class, [
                'class' => Authors::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'multiple'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez remplir ce champ.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Books::class,
        ]);
    }
}
