<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyRegistrationType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('last_name', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('username', null, [
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'translation_domain' => 'app',
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'form.element.enter-password',
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
                    'label' => 'form.element.confirm-password',
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
            ->add('phone', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('email', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('title', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('country', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('city', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('post_code', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('street', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('number', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('tin', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ]),
                    new Regex([
                        'pattern' => '/^[1-9][0-9]{8,8}$/',
                        'message' => $this->translator->trans('errors.tin-regex', [], 'app')
                    ])
                ]
            ])
            ->add('identification_number', null, [
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ],
                'attr' => [
                    'accept' => 'image/png, image/jpg'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('form.element.submit', [], 'app')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
            'csrf_protection' => false
        ]);
    }
}
