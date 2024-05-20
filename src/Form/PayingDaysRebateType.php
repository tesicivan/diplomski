<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Partner;
use App\Entity\PayingDaysRebate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\Translation\TranslatorInterface;

class PayingDaysRebateType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paying_days', NumberType::class, [
                'label' => $this->translator->trans('form.element.paying-days', [], 'app'),
                'constraints' => [
                    new PositiveOrZero([
                        'message' => $this->translator->trans('errors.non-negative', [], 'app')
                    ]),
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ],
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('rebate', NumberType::class, [
                'label' => $this->translator->trans('form.element.rebate', [], 'app'),
                'constraints' => [
                    new PositiveOrZero([
                        'message' => $this->translator->trans('errors.non-negative', [], 'app')
                    ]),
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ],
                'attr' => [
                    'class' => 'form-control mb-3'
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
            'data_class' => PayingDaysRebate::class,
            'csrf_protection' => false
        ]);
    }
}
