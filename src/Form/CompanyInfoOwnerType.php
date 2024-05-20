<?php

namespace App\Form;

use App\Entity\ActiviyCode;
use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Partner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyInfoOwnerType extends AbstractType
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
                'label' => $this->translator->trans('form.element.first-name', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('last_name', null, [
                'label' => $this->translator->trans('form.element.last-name', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('username', null, [
                'label' => $this->translator->trans('form.element.username', [], 'app'),
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('phone', null, [
                'label' => $this->translator->trans('form.element.phone', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('email', null, [
                'label' => $this->translator->trans('form.element.email', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('form.element.submit', [], 'app'),
                'attr' => [
                    'class' => 'btn btn-primary'
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
