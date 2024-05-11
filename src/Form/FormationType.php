<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title')

            ->add('description', TextareaType::class)
            
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\LessThanOrEqual('today')
                ]
            ])

            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une playlist',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'required' => false,
                'multiple' => true,
                //'expanded' => true,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner des catégories',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
