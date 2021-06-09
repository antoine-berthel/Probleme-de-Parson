<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'example@example.fr']
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prénom'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre prénom.',
                    ]),
                    new Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre nom.',
                    ]),
                    new Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mots de passe différents.',
                'options'         => ['attr' => ['class' => 'password-field col-sm-10']],
                'required'        => true,
                'first_options'   => [
                    'label'   => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe (6 caractères minimum)'],
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Mot de passe trop court',
                        ]),
                    ]
                ],
                'second_options'  => [
                    'label'   => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Confirmation'],
                ],
            ])
            ->add('roles', CollectionType::class, [
                'entry_type'    => ChoiceType::class,
                'entry_options' => [
                    'label'   => false,
                    'attr' => ['class' => 'custom-select'],
                    'choices' => [
                        'Enseignant' => 'ROLE_ENSEIGNANT',
                        'Etudiant'   => 'ROLE_ETUDIANT',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
