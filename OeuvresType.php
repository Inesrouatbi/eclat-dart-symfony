<?php

namespace App\Form;

use App\Entity\Oeuvres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OeuvresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('titre')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Peinture' => 'Peinture',
                    'Sculpture' => 'Sculpture',
                    'Gravure' => 'Gravure',
                    'Céramique' => 'Céramique',
                ],
                'placeholder' => 'Choose a category', // Optional: Add a placeholder
            ])
            ->add('description')
            ->add('img' , FileType::class, [
                'label' => 'image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('iduser' , HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvres::class,
        ]);
    }
}
