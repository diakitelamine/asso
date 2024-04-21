<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('currentPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel',
                ],
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        
                        'max' => 20,
                    ] ),
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe actuel',
                    ]),
                    new UserPassword([
                        'message' => 'Le mot de passe saisi ne correspond pas à votre mot de passe actuel',
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe saisis ne correspondent pas',
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre nouveau mot de passe',
                    ],
                    'hash_property_path' => 'password'
                    
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre nouveau mot de passe',
                    ],
                ],
                'mapped' => false,
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour le mot de passe',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ])

            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

                $form = $event->getForm();
                $user = $form->getConfig()->getOption('data');
                $passwordHasher = $form->getConfig()->getOption('passwordHasher');
                $isValid = $passwordHasher->isPasswordValid($user, $form->get('newPassword')->getData());
                if ($isValid) {
                    $form->get('newPassword')->addError(new FormError('Le mot de passe saisi ne doit pas être identique à votre mot de passe actuel'));
                }
                
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null,
        ]);
    }
}
