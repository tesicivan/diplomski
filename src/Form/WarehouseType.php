<?php

namespace App\Form;

use App\Entity\Warehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class WarehouseType extends AbstractType
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
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => $this->translator->trans('form.element.title', [], 'app'),
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('code', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => $this->translator->trans('form.element.code', [], 'app'),
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Warehouse::class,
        ]);
    }
}
