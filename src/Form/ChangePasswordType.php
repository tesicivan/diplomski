<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => $this->translator->trans('form.element.old-password', [], 'app'),
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ],
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'translation_domain' => 'app',
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'form.element.enter-new-password',
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],
                    'constraints' => [
                        new NotNull([
                            'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,12}$/',
                            'message' => $this->translator->trans('errors.password-regex', [], 'app')
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => 'form.element.confirm-new-password',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotNull([
                            'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                        ])
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('form.element.submit', [], 'app'),
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
