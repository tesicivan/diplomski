<?php

namespace App\Controller;

use App\Entity\ArticleAmount;
use App\Entity\Bill;
use App\Repository\ArticleRepository;
use App\Repository\ArticleWarehouseRepository;
use App\Repository\BillRepository;
use App\Repository\CompanyRepository;
use App\Repository\PartnerRepository;
use App\Repository\PayingTypeRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BillController extends AbstractController
{
    #[Route('/company/bill', name: 'company_bill')]
    public function bill(CompanyRepository $companyRepository): Response
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

        if ($company->getCategory()->getTitle() == 'shop')
        {
            return $this->redirectToRoute('company_shop_bill');
        }
        else
        {
            return $this->redirectToRoute('company_restaurant_bill');
        }
    }

    #[Route('/company/restaurant/bill', name: 'company_restaurant_bill')]
    public function restaurantBill(CompanyRepository $companyRepository): Response
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

        return $this->render('bill/restaurant.html.twig', [
            'user' => $this->getUser(),
            'company' => $company
        ]);
    }

    #[Route('/company/shop/bill', name: 'company_shop_bill')]
    public function shopBill(CompanyRepository $companyRepository): Response
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

        return $this->render('bill/shop.html.twig', [
            'user' => $this->getUser(),
            'company' => $company
        ]);
    }

    #[Route('/company/bill/create/{paymentId}', name: 'company_bill_create')]
    public function createBill(CompanyRepository $companyRepository, EntityManagerInterface $em, PayingTypeRepository $payingTypeRepository, PartnerRepository $partnerRepository, $paymentId): Response
    {
        $bill = new Bill();
        $payment = (int)$paymentId;

        $bill->setCompany($companyRepository->findOneBy(['user' => $this->getUser()]));
        $bill->setPayingType($payingTypeRepository->findOneBy(['id' => $payment]));
        $bill->setDate(new \DateTime());

        if ($payment == 1)
        {
            $bill->setPayed($_GET['cash-amount']);
            $bill->setCustomerIdNumber($_GET['cash-id'] ?? null);
        }
        elseif ($payment == 2)
        {
            $bill->setFirstName($_GET['check-first-name']);
            $bill->setLastName($_GET['check-last-name']);
            $bill->setCustomerIdNumber($_GET['check-id']);
        }
        elseif ($payment == 3)
        {
            $bill->setCustomerIdNumber($_GET['card-id']);
            $bill->setSlipBillNumber($_GET['card-slip']);
        }
        elseif ($payment == 4)
        {
            $bill->setPartner($partnerRepository->findOneBy(['id' => $_GET['virman-partner']]));
        }

        $em->persist($bill);
        $em->flush();

        return new JsonResponse(['bill_id' => $bill->getId()]);
    }

    #[Route('/company/bill/article-amount/{billId}/{articleId}/{warehouseId}/{amount}', name: 'company_bill_article_amount')]
    public function updateBill(CompanyRepository $companyRepository, ArticleRepository $articleRepository, WarehouseRepository $warehouseRepository, ArticleWarehouseRepository $articleWarehouseRepository, EntityManagerInterface $em, BillRepository $billRepository, $billId, $articleId, $warehouseId, $amount): Response
    {
        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);
        $articleWarehouse = $articleWarehouseRepository->findOneBy(['article' => $articleRepository->findOneBy(['id' => $articleId]), 'warehouse' => $warehouseRepository->findOneBy(['id' => $warehouseId])]);
        $articleWarehouse->setCurrentAmount($articleWarehouse->getCurrentAmount() - $amount);

        $articleAmount = new ArticleAmount();
        $articleAmount->setAmount($amount);
        $articleAmount->setArticleWarehouse($articleWarehouse);
        $articleAmount->setValue($articleWarehouse->getSellingPrice());
        if ($company->isVat())
        {
            $articleAmount->setTax($articleWarehouse->getSellingPrice() * $articleWarehouse->getArticle()->getTaxRateType()->getValue() / 100.);
        }
        $articleAmount->setBill($billRepository->findOneBy(['id' => $billId]));

        $em->persist($articleAmount);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
