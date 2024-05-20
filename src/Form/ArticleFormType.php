<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\ArticleType;
use App\Entity\Company;
use App\Entity\TaxRateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleFormType extends AbstractType
{
    private TranslatorInterface $translator;
    private Article $data;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->data = $options['data'];

        $builder
            ->add('code', null, [
                'label' => $this->translator->trans('form.element.code', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('title', null, [
                'label' => $this->translator->trans('form.element.title', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('unit', null, [
                'label' => $this->translator->trans('form.element.unit', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ]
            ])
            ->add('producer', null, [
                'label' => $this->translator->trans('form.element.producer', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('country', null, [
                'label' => $this->translator->trans('form.element.country', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('foreign_title', null, [
                'label' => $this->translator->trans('form.element.foreign-title', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('barcode', null, [
                'label' => $this->translator->trans('form.element.barcode', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
//            ->add('producer_title', null, [
//                'label' => $this->translator->trans('form.element.producer-title', [], 'app'),
//                'attr' => [
//                    'class' => 'form-control mb-3',
//                ]
//            ])
            ->add('customs_tariff', NumberType::class, [
                'label' => $this->translator->trans('form.element.customs-tariff', [], 'app') . ' [%]',
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('eco_tax', CheckboxType::class, [
                'label' => $this->translator->trans('form.element.eco-tax', [], 'app'),
                'attr' => [
                    'class' => 'form-check-input ms-2 mb-3',
                ],
                'required' => false
            ])
            ->add('excise_tax', CheckboxType::class, [
                'label' => $this->translator->trans('form.element.excise-tax', [], 'app'),
                'attr' => [
                    'class' => 'form-check-input ms-2 mb-3',
                ],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => $this->translator->trans('form.element.description', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('declaration', TextType::class, [
                'label' => $this->translator->trans('form.element.declaration', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('tax_rate_type', EntityType::class, [
                'class' => TaxRateType::class,
                'label' => $this->translator->trans('form.element.tax-rate', [], 'app'),
                'attr' => [
                    'class' => 'form-select mb-3'
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'accept' => 'image/png, image/jpg'
                ]
            ])
            ->add('supplies_amount_min', NumberType::class, [
                'label' => $this->translator->trans('form.element.supplies-amount-min', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('supplies_amount_max', NumberType::class, [
                'label' => $this->translator->trans('form.element.supplies-amount-max', [], 'app'),
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('form.element.submit', [], 'app'),
                'attr' => [
                    'class' => 'btn btn-primary mb-3'
                ]
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                foreach ($this->data->getCompany()->getWarehouses() as $warehouse)
                {
                    $form
                        ->add('warehouse_buying_price_' . $warehouse->getId(), NumberType::class, [
                            'empty_data' => 0,
                            'label' => $this->translator->trans('form.element.buying-price', [], 'app'),
                            'constraints' => [
                                new PositiveOrZero([
                                    'message' => $this->translator->trans('errors.non-negative', [], 'app')
                                ])
                            ],
                            'mapped' => false,
                            'attr' => [
                                'class' => 'form-control mb-3'
                            ]
                        ])
                        ->add('warehouse_selling_price_' . $warehouse->getId(), NumberType::class, [
                            'empty_data' => 0,
                            'label' => $this->translator->trans('form.element.selling-price', [], 'app'),
                            'constraints' => [
                                new PositiveOrZero([
                                    'message' => $this->translator->trans('errors.non-negative', [], 'app')
                                ])
                            ],
                            'mapped' => false,
                            'attr' => [
                                'class' => 'form-control mb-3'
                            ]
                        ])
                        ->add('warehouse_amount_min_' . $warehouse->getId(), NumberType::class, [
                            'empty_data' => 0,
                            'label' => $this->translator->trans('form.element.warehouse-amount-min', [], 'app'),
                            'constraints' => [
                                new PositiveOrZero([
                                    'message' => $this->translator->trans('errors.non-negative', [], 'app')
                                ])
                            ],
                            'mapped' => false,
                            'attr' => [
                                'class' => 'form-control mb-3'
                            ]
                        ])
                        ->add('warehouse_amount_max_' . $warehouse->getId(), NumberType::class, [
                            'empty_data' => 0,
                            'label' => $this->translator->trans('form.element.warehouse-amount-max', [], 'app'),
                            'constraints' => [
                                new PositiveOrZero([
                                    'message' => $this->translator->trans('errors.non-negative', [], 'app')
                                ])
                            ],
                            'mapped' => false,
                            'attr' => [
                                'class' => 'form-control mb-3'
                            ]
                        ])
                        ->add('warehouse_amount_current_' . $warehouse->getId(), NumberType::class, [
                            'empty_data' => 0,
                            'label' => $this->translator->trans('form.element.warehouse-amount-current', [], 'app'),
                            'constraints' => [
                                new PositiveOrZero([
                                    'message' => $this->translator->trans('errors.non-negative', [], 'app')
                                ])
                            ],
                            'mapped' => false,
                            'attr' => [
                                'class' => 'form-control mb-3'
                            ]
                        ])
                        ;

                }

                if ($this->data->getCompany()->getCategory()->getTitle() == 'restaurant')
                {
                    $form
                        ->add('article_type', EntityType::class, [
                            'label' => $this->translator->trans('form.element.article-type', [], 'app'),
                            'class' => ArticleType::class,
                            'required' => true,
                            'expanded' => true,
                            'choice_attr' => [
                                0 => [
                                    'class' => 'form-check-input me-2'
                                ],
                                1 => [
                                    'class' => 'form-check-input mx-2'
                                ],
                                2 => [
                                    'class' => 'form-check-input mx-2'
                                ]
                            ],
                            'attr' => [
                                'class' => 'mb-3'
                            ],
                            'constraints' => [
                                new NotNull([
                                    'message' => $this->translator->trans('errors.obligatory-select', [], 'app')
                                ])
                            ]
                        ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
