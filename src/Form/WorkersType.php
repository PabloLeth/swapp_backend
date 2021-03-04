<?php

namespace App\Form;

use App\Entity\Workers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('workerName')
            ->add('workerSurname')
            ->add('email')
            ->add('password')
            ->add('phoneNumber')
            ->add('dni')
            ->add('branch')
            ->add('role')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Workers::class,
        ]);
    }
}
