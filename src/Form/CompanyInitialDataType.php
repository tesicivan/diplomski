<?php

namespace App\Form;

use App\Entity\ActiviyCode;
use App\Entity\CompanyCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyInitialDataType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => CompanyCategory::class,
                'attr' => [
                    'class' => 'form-select mb-3'
                ],
                'label' => $this->translator->trans('form.element.category', [], 'app')
            ])
            ->add('activity_code', EntityType::class, [
                'class' => ActiviyCode::class,
                'attr' => [
                    'class' => 'form-select mb-3'
                ],
                'label' => $this->translator->trans('form.element.activity-code', [], 'app')
            ])
            ->add('vat', CheckboxType::class, [
                'label' => $this->translator->trans('form.element.vat', [], 'app'),
                'attr' => [
                    'class' => 'form-check-input mb-3 ms-2'
                ],
                'required' => false
            ])
            ->add('bank_accounts', CollectionType::class, [
                'entry_type' => BankAccountType::class,
                'entry_options' => ['label' => false],
                'label' => $this->translator->trans('form.element.bank-accounts', [], 'app'),
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('cash_registers', CollectionType::class, [
                'entry_type' => CashRegisterFormType::class,
                'entry_options' => ['label' => false],
                'label' => $this->translator->trans('form.element.cash-registers', [], 'app'),
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('warehouses', CollectionType::class, [
                'entry_type' => WarehouseType::class,
                'entry_options' => ['label' => false],
                'label' => $this->translator->trans('form.element.warehouses', [], 'app'),
                'allow_add' => true,
                'allow_delete' => true
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
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ]);
    }
}
