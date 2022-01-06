<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Course Name', 'required' => true, 'attr' => ['minlength' => 1, 'maxlength' => 30]])
            ->add('description', TextType::class, ['label' => 'Course Description', 'required' => true, 'attr' => ['minlength' => 1, 'maxlength' => 50]])
            ->add('image', FileType::class, ['label' => 'Image', 'data_class' => null, 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
