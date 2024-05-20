<?php

namespace App\Controller;

use App\Form\ArticlesSearchFormType;
use App\Repository\ArticleAmountRepository;
use App\Repository\ArticleRepository;
use App\Repository\BillRepository;
use App\Repository\CompanyRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerController extends AbstractController
{
    #[Route('/customer/companies', name: 'customer_all_companies')]
    public function companies(CompanyRepository $companyRepository): Response
    {
        if ($this->getUser()->isIsAdmin() || $this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $currentPage = $_GET['page'] ?? 1;
        $limit = 10;

        $activeCompanies = $companyRepository->findBy(['is_active' => 1], ['title' => 'asc']);

        $totalRows = count($activeCompanies);
        $totalPages = ceil($totalRows / $limit);

        return $this->render('customer/companies.html.twig', [
            'user' => $this->getUser(),
            'activeCompanies' => array_splice($activeCompanies, ($currentPage - 1) * $limit, $limit),
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'totalRows' => $totalRows
        ]);
    }

    #[Route('/customer/companies/{id}', name: 'customer_company')]
    public function singleCompany(Request $request, ArticleRepository $articleRepository, $id): Response
    {
        if ($this->getUser()->isIsAdmin() || $this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $title = $_GET['title'] ?? '';

        $form = $this->createForm(ArticlesSearchFormType::class);

        $form->get('title')->setData($title);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $getParameters = $_POST['articles_search_form'];

            foreach ($getParameters as $key => $value)
            {
                if (!$getParameters[$key] || $getParameters[$key] == '')
                    unset($getParameters[$key]);
            }

            unset($getParameters['submit']);
            unset($getParameters['_token']);

            $getParameters['id'] = $id;

            return $this->redirectToRoute('customer_company', $getParameters);
        }

        $articles = $articleRepository->getArticlesForConsumerReview($id, $title);

        return $this->render('customer/company.html.twig', [
            'user' => $this->getUser(),
            'form' => $form,
            'table' => $articles
        ]);
    }

    #[Route('/customer/analytics', name: 'customer_analytics')]
    public function analytics(CustomerRepository $customerRepository, BillRepository $billRepository, TranslatorInterface $translator): Response
    {
        if ($this->getUser()->isIsAdmin() || $this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }
        // current month

        $customerIdNumber = $customerRepository->findOneBy(['user' => $this->getUser()])->getIdNumber();

        $unprocessedDataCurrentMonth = $billRepository->getCurrentMonthDataForUser($customerIdNumber);

//        dd($unprocessedDataCurrentMonth);

        $labelsCurrentMonth = [];
        $dataCurrentMonth = [];

        $currentDay = (int)date('d');
        $currentMonth = (int)date('m');

        for ($i = 1; $i <= $currentDay; $i++)
        {
            $dataCurrentMonth[$i] = 0;
            $labelsCurrentMonth[] = '' . ($i < 10 ? '0' . $i : $i);
        }

        foreach ($unprocessedDataCurrentMonth as $data)
        {
            $dataCurrentMonth[(int)explode('-', $data['date'])[2]] += (int)$data['amount'];
        }

        // last 12 months
        $monthAbbr = [
            "'" . $translator->trans('month.jan', [], 'app') . "'",
            "'" . $translator->trans('month.feb', [], 'app') . "'",
            "'" . $translator->trans('month.mar', [], 'app') . "'",
            "'" . $translator->trans('month.apr', [], 'app') . "'",
            "'" . $translator->trans('month.may', [], 'app') . "'",
            "'" . $translator->trans('month.jun', [], 'app') . "'",
            "'" . $translator->trans('month.jul', [], 'app') . "'",
            "'" . $translator->trans('month.aug', [], 'app') . "'",
            "'" . $translator->trans('month.sep', [], 'app') . "'",
            "'" . $translator->trans('month.oct', [], 'app') . "'",
            "'" . $translator->trans('month.nov', [], 'app') . "'",
            "'" . $translator->trans('month.dec', [], 'app') . "'"
        ];

        $monthsLabels = [];
        $monthsDataTemp = [];
        $monthsData = [];

        foreach ($monthAbbr as $key => $abbr)
        {
            $monthsDataTemp[] = 0;
            $monthsLabels[] =  $monthAbbr[($key + $currentMonth) % 12];
        }

        $unprocessedDataLastYear = $billRepository->getLastYearDataForUser($customerIdNumber);

        foreach ($unprocessedDataLastYear as $data)
        {
            $monthsDataTemp[$data['month'] - 1] = (int)$data['amount'];
        }

        foreach ($monthsDataTemp as $key => $data)
        {
            $monthsData[] = $monthsDataTemp[($key + $currentMonth) % 12];
        }

        $table = $billRepository->getAnalyticsForUser($customerIdNumber);

        return $this->render('customer/analytics.html.twig', [
            'user' => $this->getUser(),
            'labelsCurrentMonth' => $labelsCurrentMonth,
            'dataCurrentMonth' => $dataCurrentMonth,
            'labelsLastYear' => $monthsLabels,
            'dataLastYear' => $monthsData,
            'table' => $table
        ]);
    }

    #[Route('/customer/analytics/{id}', name: 'customer_analytic')]
    public function singleAnalytic(BillRepository $billRepository, ArticleAmountRepository $articleAmountRepository, $id): Response
    {
        if ($this->getUser()->isIsAdmin() || $this->getUser()->isIsCompany())
        {
            return $this->render('exception/index.html.twig');
        }

        $bill = $billRepository->findOneBy(['id' => $id]);
        $articles = $articleAmountRepository->getAllForCustomerAnalytic($id);

        return $this->render('customer/analytic.html.twig', [
            'user' => $this->getUser(),
            'bill' => $bill,
            'articles' => $articles
        ]);
    }
}
