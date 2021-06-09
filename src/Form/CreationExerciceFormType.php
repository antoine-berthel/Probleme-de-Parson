<?php

namespace App\Form;

use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreationExerciceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de l\'éxercice'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un nom',
                    ]),
                    new Length([
                        'max' => 254,
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Déscription de l\'éxercice', 'wrap' => 'off'],
                'required'   => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('problem', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => '10', 'cols' => '30', 'placeholder' => 'Tapez votre code ici', 'wrap' => 'off'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous ne pouvez pas créer un problème vide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}
