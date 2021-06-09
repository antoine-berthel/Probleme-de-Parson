<?php

namespace App\Form;

use App\Entity\Solution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreationSolutionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('solution', TextareaType::class, [
                'attr' => ['class' => 'form-control col-sm-7', 'rows' => '10', 'cols' => '30', 'placeholder' => 'Tapez votre code ici', 'wrap' => 'off'],
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
            'data_class' => Solution::class,
        ]);
    }
}
