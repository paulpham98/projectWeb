<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Room Name',
                'required' => true,
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 30
                ]
            ])
            ->add('building',TextType::class,[
                'label' => 'Building Name',
                'required' => true,
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 30
                ]
            ])
            ->add('teacher', EntityType::class,[
                'label' => 'Teacher',
                'required' => true,
                'class' => Teacher::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true
            ])
            ->add('avatar', FileType::class,[
                'label' => 'Image Room',
                'data_class' => null,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
