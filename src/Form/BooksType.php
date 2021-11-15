<?php

namespace App\Form;

use App\Entity\Authors;
use App\Entity\Books;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BooksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre',
            ])
            ->add('description',TextareaType::class,[
                'label' => 'Synopsis',
                'attr' => ['class'=> 'materialize-textarea'],
            ])
            ->add('parutedAt', DateType::class, [
                'label' => 'Date de parution',
                'widget' => 'single_text',
            ])
            ->add('cover', FileType::class, [
                'label' => 'Couverture',
                'label_attr' => ['style' => 'color: #FFFFFF'],
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Type(s) de livre',
                'class' => Categories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'multiple'],
            ])
            ->add('authors', EntityType::class, [
                'label' => 'Auteur(s)',
                'class' => Authors::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'multiple'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Books::class,
        ]);
    }
}
