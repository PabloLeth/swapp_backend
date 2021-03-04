<?php

namespace App\Form;

use App\Entity\Shift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weekId')
            ->add('dayId')
            ->add('startShift')
            ->add('endShift')
            ->add('swapping')
            ->add('branch')
            ->add('shiftType')
            ->add('worker')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shift::class,
        ]);
    }
}
