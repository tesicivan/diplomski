<?php

namespace App\Form;

use App\Entity\ActiviyCode;
use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Partner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyInfoCompanyType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => $this->translator->trans('form.element.title', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('street', null, [
                'label' => $this->translator->trans('form.element.street', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('number', null, [
                'label' => $this->translator->trans('form.element.number', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('post_code', null, [
                'label' => $this->translator->trans('form.element.post-code', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('city', null, [
                'label' => $this->translator->trans('form.element.city', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('country', null, [
                'label' => $this->translator->trans('form.element.country', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('tin', null, [
                'label' => $this->translator->trans('form.element.tin', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
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
                'label' => $this->translator->trans('form.element.id-number', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('vat', CheckboxType::class, [
                'label' => $this->translator->trans('form.element.vat', [], 'app'),
                'attr' => [
                    'class' => 'form-check-input mb-3 ms-2',
                ],
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => CompanyCategory::class,
                'choice_label' => 'title',
                'label' => $this->translator->trans('form.element.category', [], 'app'),
                'attr' => [
                    'class' => 'form-select mb-3'
                ]
            ])
            ->add('activity_code', EntityType::class, [
                'class' => ActiviyCode::class,
                'label' => $this->translator->trans('form.element.activity-code', [], 'app'),
                'attr' => [
                    'class' => 'form-select mb-3'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/png, image/jpg',
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
            'data_class' => Company::class,
        ]);
    }
}
