<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchBook', SearchType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => ['id' => 'search-input', 'placeholder' => 'Rechercher un livre' ],
                'required' => false
            ] )
            ->add('filterByGenre', EntityType::class, [
                'label' => false,
                'class' => Categories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'multiple'],
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
