<?php

namespace App\Controller;

use App\Entity\ArticleCategory;
use App\Form\ArticleCategoryType;
use App\Repository\ArticleCategoryRepository;
use App\Repository\ArticleRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/company/category', name: 'company_categories')]
    public function categories(Request $request, EntityManagerInterface $em, ArticleRepository $articleRepository, CompanyRepository $companyRepository): Response
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

        $categories = $company->getArticleCategories();
        $articles = $articleRepository->findBy(['article_category' => null, 'company' => $company]);

        $articleCategory = new ArticleCategory();
        $articleCategory->setCompany($company);

        $form = $this->createForm(ArticleCategoryType::class, $articleCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($articleCategory);
            $em->flush();

            return $this->redirectToRoute('company_categories');
        }

        return $this->render('category/categories.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'table' => $categories,
            'articles' => $articles,
            'form' => $form
        ]);
    }

    #[Route('company/category/remove/{articleId}/{categoryId}', name: 'company_category_remove')]
    public function removeCategory(EntityManagerInterface $em, ArticleRepository $articleRepository, $articleId, $categoryId)
    {
        $article = $articleRepository->findOneBy(['id' => $articleId]);
        $article->setArticleCategory(null);

        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute('company_category', [
            'id' => $categoryId
        ]);
    }

    #[Route('/company/category/{id}', name: 'company_category')]
    public function category(ArticleCategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, ArticleRepository $articleRepository, CompanyRepository $companyRepository, $id): Response
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

        $category = $categoryRepository->findOneBy(['id' => $id]);

        $articles = $articleRepository->findBy(['article_category' => $category]);

        return $this->render('category/category.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'table' => $articles,
            'category' => $category
        ]);
    }

    #[Route('/company/category/add-article-to-category/{categoryId}/{articleId}', name: 'add_article_to_category')]
    public function addArticleToCategory(CompanyRepository $companyRepository, EntityManagerInterface $em, ArticleRepository $articleRepository, ArticleCategoryRepository $articleCategoryRepository, $categoryId, $articleId)
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

        $category = $articleCategoryRepository->findOneBy(['id' => $categoryId]);
        $article = $articleRepository->findOneBy(['id' => $articleId]);

        $article->setArticleCategory($category);
        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute('company_categories');
    }

}
