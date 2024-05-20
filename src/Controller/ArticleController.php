<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleWarehouse;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\ArticleWarehouseRepository;
use App\Repository\CompanyRepository;
use App\Repository\TaxRateTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleController extends AbstractController
{
    #[Route('/company/articles', name: 'company_articles')]
    public function index(CompanyRepository $companyRepository, ArticleRepository $articleRepository): Response
    {
        if (!$this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $currentPage = $_GET['page'] ?? 1;
        $limit = 10;

        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        if (!$company->isIsActive())
        {
            return $this->render('company/not-active.html.twig', [
                'company' => $company,
                'user' => $this->getUser()
            ]);
        }

        $articles = $articleRepository->findBy(['company' => $company, 'active' => true], ['code' => 'asc'], $limit, ($currentPage - 1) * $limit);

        $totalRows = $articleRepository->count(['company' => $company, 'active' => true]);
        $totalPages = ceil($totalRows / $limit);

        return $this->render('article/articles.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'table' => $articles,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'totalRows' => $totalRows
        ]);
    }

    #[Route('/company/articles/edit/{id}', name: 'company_articles_edit', defaults: ['id' => null])]
    public function edit(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, ArticleWarehouseRepository $articleWarehouseRepository, TaxRateTypeRepository $taxRateTypeRepository, CompanyRepository $companyRepository, ArticleRepository $articleRepository, $id): Response
    {
        if (!$this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $article = $id ? $articleRepository->findOneBy(['id' => $id]) : new Article();
        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        if ($article == null || ($article->getCompany() != $company && $id != null))
        {
            return $this->render('exception/not-found.html.twig');
        }

        if (!$company->isIsActive())
        {
            return $this->render('company/not-active.html.twig', [
                'company' => $company,
                'user' => $this->getUser()
            ]);
        }

        $article->setCompany($company);
        if (!$company->isVat())
        {
            $article->setTaxRateType($taxRateTypeRepository->findOneBy(['value' => 0]));
        }

        $form = $this->createForm(ArticleFormType::class, $article);

        foreach ($company->getWarehouses() as $warehouse)
        {
            $articleWarehouse = $articleWarehouseRepository->findOneBy(['article' => $article, 'warehouse' => $warehouse]);
            if (!$articleWarehouse)
            {
                $form->get('warehouse_buying_price_' . $warehouse->getId())->setData(0);
                $form->get('warehouse_selling_price_' . $warehouse->getId())->setData(0);
                $form->get('warehouse_amount_min_' . $warehouse->getId())->setData(0);
                $form->get('warehouse_amount_max_' . $warehouse->getId())->setData(0);
                $form->get('warehouse_amount_current_' . $warehouse->getId())->setData(0);
            }
            else
            {
                $form->get('warehouse_buying_price_' . $warehouse->getId())->setData($articleWarehouse->getBuyingPrice());
                $form->get('warehouse_selling_price_' . $warehouse->getId())->setData($articleWarehouse->getSellingPrice());
                $form->get('warehouse_amount_min_' . $warehouse->getId())->setData($articleWarehouse->getMinAmount());
                $form->get('warehouse_amount_max_' . $warehouse->getId())->setData($articleWarehouse->getMaxAmount());
                $form->get('warehouse_amount_current_' . $warehouse->getId())->setData($articleWarehouse->getCurrentAmount());
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $error = false;

            $tmpFile = $_FILES['article_form']['tmp_name']['image'] != '' ? new File($_FILES['article_form']['tmp_name']['image']) : null;

            if ($tmpFile != null)
            {
                $imgWidth = getimagesize($tmpFile)[0];
                $imgHeight = getimagesize($tmpFile)[1];

                $incorrectImageSize = $imgWidth < 100 || $imgWidth > 300 || $imgHeight < 100 || $imgHeight > 300;

                if ($incorrectImageSize)
                {
                    $form->get('image')->addError(new FormError($translator->trans('errors.image-size', [], 'app')));
                    $error = true;
                }
            }

            $codeNotUnique = $articleRepository->findOneBy(['code' => $form->get('code')->getData(), 'company' => $company, 'active' => true]) != null && $articleRepository->findOneBy(['code' => $form->get('code')->getData(), 'company' => $company, 'active' => true]) != $article;

            if ($codeNotUnique)
            {
                $form->get('code')->addError(new FormError($translator->trans('errors.code-not-unique', [], 'app')));

                $error = true;
            }

            if ($error)
            {
                return $this->render('article/edit.html.twig', [
                    'user' => $this->getUser(),
                    'company' => $company,
                    'form' => $form
                ]);
            }

            if ($tmpFile)
            {
                $extension = explode('/', $_FILES['article_form']['type']['image'])[1];
                $newFileFullPath = 'server_simulation/' . $form->get('code')->getData() . '.' . $extension;
                copy($tmpFile, $newFileFullPath);
                $article->setImageUrl($newFileFullPath);
            }

            foreach ($company->getWarehouses() as $warehouse)
            {
                $articleWarehouse = $articleWarehouseRepository->findOneBy(['article' => $article, 'warehouse' => $warehouse]) ?? new ArticleWarehouse();

                $articleWarehouse->setWarehouse($warehouse);
                $articleWarehouse->setArticle($article);
                $articleWarehouse->setBuyingPrice($form->get('warehouse_buying_price_' . $warehouse->getId())->getData());
                $articleWarehouse->setSellingPrice($form->get('warehouse_selling_price_' . $warehouse->getId())->getData());
                $articleWarehouse->setMinAmount($form->get('warehouse_amount_min_' . $warehouse->getId())->getData());
                $articleWarehouse->setMaxAmount($form->get('warehouse_amount_max_' . $warehouse->getId())->getData());
                $articleWarehouse->setCurrentAmount($form->get('warehouse_amount_current_' . $warehouse->getId())->getData());

                $em->persist($articleWarehouse);
            }

            $article->setActive(true);

            if (!$company->isVat())
            {
                $article->setTaxRateType($taxRateTypeRepository->findOneBy(['id' => 3]));
            }

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('company_articles');
        }

        return $this->render('article/edit.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'form' => $form
        ]);
    }

    #[Route('/company/articles/remove/{id}', name: 'company_articles_remove')]
    public function remove(CompanyRepository $companyRepository, EntityManagerInterface $em, ArticleRepository $articleRepository, $id): Response
    {
        if (!$this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        if (!$company->isIsActive())
        {
            return $this->render('company/not-active.html.twig', [
                'company' => $company,
                'user' => $this->getUser()
            ]);
        }

        $article = $articleRepository->findOneBy(['id' => $id]);

        if ($article == null || ($article->getCompany() != $company && $id != null))
        {
            return $this->render('exception/not-found.html.twig');
        }

        $article->setActive(false);

        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute('company_articles');
    }

    #[Route('/company/articles/fetch-all', name: 'company_articles_fetch_all')]
    public function fetchAll(CompanyRepository $companyRepository, ArticleRepository $articleRepository, ArticleWarehouseRepository $articleWarehouseRepository)
    {
        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        $articles = $articleRepository->findBy(['company' => $company, 'active' => true]);

        $articleWarehouses = $articleWarehouseRepository->findBy(['article' => $articles]);

        $response = [];

        foreach ($articleWarehouses as $articleWarehouse)
        {
            $response[] = [
                'article_id' => $articleWarehouse->getArticle()->getId(),
                'article_title' => $articleWarehouse->getArticle()->getTitle(),
                'warehouse_id' => $articleWarehouse->getWarehouse()->getId(),
                'warehouse_title' => $articleWarehouse->getWarehouse()->getTitle(),
                'current_amount' => $articleWarehouse->getCurrentAmount(),
                'price' => $articleWarehouse->getSellingPrice()
            ];
        }

        return new JsonResponse($response);
    }

}
