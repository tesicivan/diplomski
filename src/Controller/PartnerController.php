<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\PayingDaysRebate;
use App\Form\PartnerType;
use App\Form\PayingDaysRebateType;
use App\Repository\CompanyRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PartnerController extends AbstractController
{
    #[Route('/company/partners', name: 'company_partners')]
    public function index(Request $request, CompanyRepository $companyRepository, PartnerRepository $partnerRepository): Response
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

        $table = $partnerRepository->getAll($company->getId());

        $otherPartners = $partnerRepository->getOtherPartners($company->getId(), $company->getTin());

        return $this->render('partner/partners.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'table' => $table,
            'otherPartners' => $otherPartners
        ]);
    }

    #[Route('/company/partners/create', name: 'company_partners_create')]
    public function create(Request $request, TranslatorInterface $translator, CompanyRepository $companyRepository, PartnerRepository $partnerRepository, EntityManagerInterface $em): Response
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

        $partner = new Partner();

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('tin')->getData() == $company->getTin())
            {
                $form->get('tin')->addError(new FormError($translator->trans('errors.this-company', [], 'app')));

                return $this->render('partner/create.html.twig', [
                    'user' => $this->getUser(),
                    'company' => $company,
                    'form' => $form
                ]);
            }

            if ($partnerRepository->findOneBy(['tin' => $form->get('tin')->getData()]))
            {
                $form->get('tin')->addError(new FormError($translator->trans('errors.tin-not-unique', [], 'app')));

                return $this->render('partner/create.html.twig', [
                    'user' => $this->getUser(),
                    'company' => $company,
                    'form' => $form
                ]);
            }

            $em->persist($partner);

            $company->addPartner($partner);
            $em->persist($company);

            $payingDaysRebate = new PayingDaysRebate();
            $payingDaysRebate->setPayingDays($form->get('paying_days')->getData());
            $payingDaysRebate->setRebate($form->get('rebate')->getData());
            $payingDaysRebate->setPartner($partner);
            $payingDaysRebate->setCompany($company);
            $em->persist($payingDaysRebate);

            $em->flush();

            return $this->redirectToRoute('company_partners');
        }

        return $this->render('partner/create.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'form' => $form
        ]);
    }

    #[Route('/company/partners/create-tin/{id}', name: 'company_partners_create_tin')]
    public function createTin(Request $request, CompanyRepository $companyRepository, PartnerRepository $partnerRepository, EntityManagerInterface $em, $id): Response
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

        $partner = $partnerRepository->findOneBy(['id' => $id]);

        $payingDaysRebate = new PayingDaysRebate();

        $form = $this->createForm(PayingDaysRebateType::class, $payingDaysRebate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $payingDaysRebate->setPartner($partner);
            $payingDaysRebate->setCompany($company);
            $em->persist($payingDaysRebate);

            $company->addPartner($partner);
            $em->persist($company);

            $em->flush();

            return $this->redirectToRoute('company_partners');
        }

        return $this->render('partner/create-tin.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'form' => $form
        ]);
    }

    #[Route('/company/partners/fetch-all', name: 'company_partners_fetch_all')]
    public function fetchAll(CompanyRepository $companyRepository)
    {
        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        $partners = $company->getPartners();

        $response = [];

        foreach ($partners as $partner)
        {
            $response[] = [
                'partner_id' => $partner->getId(),
                'partner_title' => $partner->getTitle(),
                'partner_tin' => $partner->getTin()
            ];
        }

        return new JsonResponse($response);
    }
}
