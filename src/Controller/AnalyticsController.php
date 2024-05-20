<?php

namespace App\Controller;

use App\Form\DateSearchType;
use App\Repository\ArticleAmountRepository;
use App\Repository\BillRepository;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnalyticsController extends AbstractController
{
    #[Route('/company/analytics/review', name: 'company_analytics_review')]
    public function review(CompanyRepository $companyRepository, BillRepository $billRepository): Response
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

        if (array_key_exists('date_search', $_GET) && array_key_exists('date', $_GET['date_search']))
        {
            $date = new \DateTime($_GET['date_search']['date']);
        }
        else
        {
            $date = new \DateTime();
        }

        if (array_key_exists('date_search', $_GET) && array_key_exists('dateTo', $_GET['date_search']))
        {
            $dateTo = new \DateTime($_GET['date_search']['dateTo']);
        }
        else
        {
            $dateTo = new \DateTime();
        }

        $form = $this->createForm(DateSearchType::class);

        if ($date->format('d-m-Y') > $dateTo->format('d-m-Y'))
        {
            $dateTo = $date;
            $form->get('date')->addError(new FormError('Unesite validan vremenski opseg.'));
            $form->get('dateTo')->addError(new FormError('Unesite validan vremenski opseg.'));
        }

        $form->get('date')->setData($date);
        $form->get('dateTo')->setData($dateTo);

        $bills = $billRepository->getBillsForDate($date, $dateTo, $company->getId());

        $totalAmount = 0;
        $totalTax = 0;

//        dd($bills);

        foreach ($bills as $bill)
        {
            $totalAmount += (int)$bill['value'];
            $totalTax += (int)$bill['tax'];
        }

        return $this->render('analytics/review.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'table' => $bills,
            'form' => $form,
            'totalTax' => $totalTax,
            'totalAmount' => $totalAmount,
            'date' => $date,
            'dateTo' => $dateTo
        ]);
    }

    #[Route('/company/analytics/bill/{id}', name: 'company_analytics_bill')]
    public function bill(CompanyRepository $companyRepository, BillRepository $billRepository, ArticleAmountRepository $articleAmountRepository, $id): Response
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

        $bill = $billRepository->findOneBy(['id' => $id]);
        $articles = $articleAmountRepository->getAllForCustomerAnalytic($id);

        return $this->render('analytics/bill.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'bill' => $bill,
            'articles' => $articles
        ]);
    }
}
