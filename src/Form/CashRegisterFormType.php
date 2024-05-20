<?php

namespace App\Form;

use App\Entity\CashRegister;
use App\Entity\CashRegisterType;
use App\Entity\Company;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class CashRegisterFormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => $this->translator->trans('form.element.location', [], 'app'),
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => CashRegisterType::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => $this->translator->trans('form.element.type', [], 'app')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashRegister::class,
        ]);
    }
}
