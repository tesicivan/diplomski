<?php

namespace App\Form;

use App\Entity\ArticleCategory;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleCategoryType extends AbstractType
{
    private TranslatorInterface $translator;
    private ArticleCategory $data;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->data = $options['data'];

        $builder
            ->add('title', null, [
                'label' => $this->translator->trans('category.table.title', [], 'app'),
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('errors.obligatory-field', [], 'app')
                    ])
                ],
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('parent_category', EntityType::class, [
                'label' => $this->translator->trans('category.table.parent-category', [], 'app'),
                'required' => false,
                'class' => ArticleCategory::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-select mb-3'
                ],
                'query_builder' => function($repository): QueryBuilder
                {
                    return
                        $repository
                            ->createQueryBuilder('e')
                            ->where("e.company = " . $this->data->getCompany()->getId());
                }
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
            'data_class' => ArticleCategory::class,
        ]);
    }
}
