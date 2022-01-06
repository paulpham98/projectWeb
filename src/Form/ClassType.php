<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseClass;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Class Name', 'required' => true, 'attr' => ['minlength' => 1, 'maxlength' => 30]])
            ->add('maximum', IntegerType::class, ['label' => 'Maximum Students', 'required' => true, 'attr' => ['min' => 1, 'max' => 40]])
            ->add('startdate', DateType::class, ['label' => 'Start Date', 'required' => true, 'widget' => 'single_text'])
            ->add('enddate', DateType::class, ['label' => 'Start Date', 'required' => true, 'widget' => 'single_text'])
            ->add('course', EntityType::class, [
                'label' => 'Course',
                'required' => true,
                'class' => Course::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true
            ]);
        // ->add('student', EntityType::class, [
        //     'label' => 'Student',
        //     'required' => true,
        //     'class' => Student::class,
        //     'choice_label' => 'name',
        //     'multiple' => true,
        //     'expanded' => true
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseClass::class,
        ]);
    }
}
