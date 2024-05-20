<?php

namespace App\Form;

use App\Entity\BankAccount;
use App\Entity\Company;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class BankAccountType extends AbstractType
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
                'label' => $this->translator->trans('form.element.bank-account', [], 'app'),
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{3,3}-[0-9]{12,12}-[0-9]{2,2}$/',
                        'message' => $this->translator->trans('errors.bank-account-regex', [], 'app')
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BankAccount::class,
        ]);
    }
}
