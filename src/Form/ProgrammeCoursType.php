<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProgrammeCoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choices' => $options['nonProgrammes'],
                'choice_label' => 'intitule',
                'label' => 'Cours'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (jours)',
                'attr' => [
                    'min' => 1,
                    'max' => 365
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
            'nonProgrammes' => []
        ]);
    }
}