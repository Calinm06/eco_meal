<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\Category;
use App\Entity\Package;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('description', TextType::class)
            ->add('price', NumberType::class)
            ->add('created_at', DateType::class)
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('photo',FileType::class,[
                'required' => 'false',
                'mapped' => 'false',
                'label' => 'Photo'
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
       $resolver->setDefaults([
           'data_class' => Package::class,
       ]);
    }
}
