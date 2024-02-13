<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Email', 'spellcheck' => 'false'],
                'constraints' => [
                    new NotBlank(message: 'Vous devez renseigner une adresse mail.'),
                    new Email(message: "L'adresse mail fourni n'est pas valide.")
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
                'first_options' => ['attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Mot de passe'],
                    'label' => false
                ],
                'second_options' => ['attr' =>
                    ['placeholder' => 'Réentrer votre mot de passe'],
                    'label' => false
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'attr' => ['class' => 'gender-choice'],
                'label' => false,
                'choices' => [
                    'M.' => false,
                    'Mme.' => true,
                ],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new NotNull(message: "Vous devez choisir votre genre.")
                ],
                'choice_attr' => function() {
                    return ['class' => 'radio-button'];
                }
            ])
            ->add('firstName', options: [
                'label' => false,
                'attr' => ['placeholder' => 'Prénom', 'spellcheck' => 'false'],
                'constraints' => [
                    new NotBlank(message: "Veuillez renseigner un prénom."),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom doit faire au moins {{ limit }} caractères.',
                        'max' => 32,
                    ]),
                    new Callback(function (string $firstName, ExecutionContextInterface $context) {
                        if (!preg_match('/^[\p{L}\s-]+$/u', $firstName)) {
                            $context->buildViolation('Le prénom ne doit contenir que des lettres et/ou des tirets.')
                                ->addViolation();
                        }
                    })
                ],
            ])
            ->add('lastName', options: [
                'label' => false,
                'attr' => ['placeholder' => 'Nom de famille', 'spellcheck' => 'false'],
                'constraints' => [
                    new NotBlank(message: "Veuillez renseigner un nom."),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères.',
                        'max' => 32,
                    ]),
                    new Callback(function (string $lastName, ExecutionContextInterface $context) {
                        if (!preg_match('/^[\p{L}\s-]+$/u', $lastName)) {
                            $context->buildViolation('Le nom ne doit contenir que des lettres et/ou des tirets.')
                                ->addViolation();
                        }
                    })
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'attr' => ['class' => 'checkbox'],
                'label' => "J'accepte les condititions d'utilisation",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez accepter les conditions d'utilisations.",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
