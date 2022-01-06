<?php

namespace App\Form;

use App\Entity\CourseClass;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Student Name', 'required' => true, 'attr' => ['minlength' => 5, 'maxlength' => 20]])
            ->add('birthday', DateType::class, ['label' => 'Birthday', 'required' => true, 'format' => 'dd MM yyyy', 'years' => range(1998, 2003)])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
            ->add('phonenumber', IntegerType::class, ['label' => 'Phone Number', 'required' => true, 'attr' => ['minlength' => 10, 'maxlength' => 10]])
            ->add('avatar', FileType::class, ['label' => "Student Image", 'data_class' => null, 'required' => false]);
        // ->add('courseClasses', EntityType::class, ['label' => 'Class', 'required' => true, 'class' => CourseClass::class, 'choice_label' => 'name', 'multiple' => true, 'expanded' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
