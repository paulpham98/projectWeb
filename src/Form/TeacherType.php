<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Teacher name',
                'required' => true,
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 30
                ]
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Teacher Birthday',
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('mobile', NumberType::class, [
                'label' => 'Teacher Mobile',
                'required' => true,
                'attr' => [
                    'minlength' => 10,
                    'maxlength' => 10
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Teacher Email',
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'Teacher Address',
                'required' => true,
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 30
                ]
            ])
            ->add('nationality', TextType::class, [
                'label' => 'Teacher Nationality',
                'required' => true,
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 30
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'data_class' => null,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
