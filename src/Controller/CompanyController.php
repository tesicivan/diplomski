<?php

namespace App\Controller;

use App\Form\CompanyInfoBankAccountType;
use App\Form\CompanyInfoCashRegisterType;
use App\Form\CompanyInfoCompanyType;
use App\Form\CompanyInfoOwnerType;
use App\Form\CompanyInfoWarehouseType;
use App\Form\CompanyInitialDataType;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyController extends AbstractController
{
    #[Route('/company', name: 'company')]
    public function index(CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->findOneBy(['user' => $this->getUser()]);

        if ($company->isIsActive())
        {
            if ($company->isIsConfigured())
            {
                return $this->redirectToRoute('company_info');
            }
            else
            {
                return $this->redirectToRoute('company_initial_data');
            }
        }
        else
        {
            return $this->render('company/not-active.html.twig', [
                'company' => $company,
                'user' => $this->getUser()
            ]);
        }
    }

    #[Route('/company/info', name: 'company_info')]
    public function info(Request $request, CompanyRepository $companyRepository): Response
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

        return $this->render('company/info.html.twig', [
            'user' => $this->getUser(),
            'company' => $company
        ]);
    }

    #[Route('/company/initial-data', name: 'company_initial_data')]
    public function companyInitialData(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, CompanyRepository $companyRepository, WarehouseRepository $warehouseRepository): Response
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

        $form = $this->createForm(CompanyInitialDataType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $noBankAccounts = false;
            $noWarehouses = false;
            $noCashRegisters = false;
            $warehouseCodeNotUnique = false;

            if (count($form->get('bank_accounts')->getData()) == 0)
            {
                $noBankAccounts = true;
            }

            if (count($form->get('warehouses')->getData()) == 0)
            {
                $noWarehouses = true;
            } else {
                $warehouseCodes = [];
                foreach ($form->get('warehouses') as $warehouse)
                {
                    $warehouseCodes[] = $warehouse->getData()->getCode();
                    if (count($warehouseCodes) != count(array_unique($warehouseCodes)) || $warehouseRepository->findOneBy(['code' => $warehouse->getData()->getCode()]))
                    {
                        $warehouse->get('code')->addError(new FormError($translator->trans('errors.unique', [], 'app')));
                        $warehouseCodeNotUnique = true;
                    }
                }
            }

            if (count($form->get('cash_registers')->getData()) == 0)
            {
                $noCashRegisters = true;
            }

            if ($noCashRegisters || $noWarehouses || $noBankAccounts || $warehouseCodeNotUnique)
            {
                return $this->render('company/initial-data.html.twig', [
                    'user' => $this->getUser(),
                    'company' => $company,
                    'form' => $form,
                    'noBankAccounts' => $noBankAccounts,
                    'noWarehouses' => $noWarehouses,
                    'noCashRegisters' => $noCashRegisters
                ]);
            }

            foreach ($form->get('bank_accounts')->getData() as $bankAccount)
            {
                $bankAccount->setCompany($company);
                $company->addBankAccount($bankAccount);
                $em->persist($bankAccount);
            }

            foreach ($form->get('warehouses')->getData() as $warehouse)
            {
                $warehouse->setCompany($company);
                $company->addWarehouse($warehouse);
                $em->persist($warehouse);
            }

            foreach ($form->get('cash_registers')->getData() as $cashRegister)
            {
                $cashRegister->setCompany($company);
                $company->addCashRegister($cashRegister);
                $em->persist($cashRegister);
            }

            $company->setActivityCode($form->get('activity_code')->getData());
            $company->setCategory($form->get('category')->getData());
            $company->setVat($form->get('vat')->getData());
            $company->setIsConfigured(true);

            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/initial-data.html.twig', [
            'user' => $this->getUser(),
            'company' => $company,
            'form' => $form,
            'noBankAccounts' => false,
            'noWarehouses' => false,
            'noCashRegisters' => false
        ]);
    }

    #[Route('/company/edit/owner', name: 'company_edit_owner')]
    public function companyEditOwner(Request $request, CompanyRepository $companyRepository, UserRepository $userRepository, TranslatorInterface $translator, EntityManagerInterface $em)
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

        $form = $this->createForm(CompanyInfoOwnerType::class, $company);
        $form->get('username')->setData($this->getUser()->getUsername());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $usernameNotUnique = false;
            $emailNotUnique = false;

            $userCheck = $userRepository->findOneBy(['username' => $form->get('username')->getData()]);
            if ($userCheck != null && $userCheck != $this->getUser()) {
                $form->get('username')->addError(new FormError($translator->trans('errors.username-not-unique', [], 'app')));
                $usernameNotUnique = true;
            }

            $companyCheck = $companyRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($companyCheck != null && $companyCheck != $company) {
                $form->get('email')->addError(new FormError($translator->trans('errors.email-not-unique', [], 'app')));
                $emailNotUnique = true;
            }

            if ($emailNotUnique || $usernameNotUnique)
            {
                return $this->render('company/edit-owner.html.twig', [
                    'company' => $company,
                    'user' => $this->getUser(),
                    'form' => $form
                ]);
            }

            $this->getUser()->setUsername($form->get('username')->getData());

            $em->persist($this->getUser());
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/edit-owner.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'form' => $form
        ]);
    }

    #[Route('/company/edit/company', name: 'company_edit_company')]
    public function companyEditCompany(Request $request, CompanyRepository $companyRepository, UserRepository $userRepository, TranslatorInterface $translator, EntityManagerInterface $em)
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

        $form = $this->createForm(CompanyInfoCompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $tinNotUnique = false;

            $companyCheck = $companyRepository->findOneBy(['tin' => $form->get('tin')->getData()]);

            if ($companyCheck != null && $companyCheck != $company) {
                $form->get('tin')->addError(new FormError($translator->trans('errors.tin-not-unique', [], 'app')));
                $tinNotUnique = true;
            }

            $incorrectImageSize = false;

            if ($_FILES['company_info_company']['tmp_name']['image'] != "") {
                $tmpFile = new File($_FILES['company_info_company']['tmp_name']['image']);

                $imgWidth = getimagesize($tmpFile)[0];
                $imgHeight = getimagesize($tmpFile)[1];

                $incorrectImageSize = $imgWidth < 100 || $imgWidth > 300 || $imgHeight < 100 || $imgHeight > 300;

                if ($incorrectImageSize)
                {
                    $form->get('image')->addError(new FormError($translator->trans('errors.image-size', [], 'app')));
                }
            }

            if ($tinNotUnique || $incorrectImageSize)
            {
                return $this->render('company/edit-company.html.twig', [
                    'company' => $company,
                    'user' => $this->getUser(),
                    'form' => $form
                ]);
            }

            if ($_FILES['company_info_company']['tmp_name']['image'] != "") {
                $extension = explode('/', $_FILES['company_info_company']['type']['image'])[1];
                $newFileFullPath = 'server_simulation/' . $this->getUser()->getUsername() . '.' . $extension;
                copy($tmpFile, $newFileFullPath);

                $company->setLogoUrl($newFileFullPath);
            }

            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/edit-company.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'form' => $form
        ]);
    }

    #[Route('/company/edit/bank-accounts', name: 'company_edit_bank_accounts')]
    public function companyEditBankAccounts(Request $request, CompanyRepository $companyRepository, EntityManagerInterface $em)
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

        $bankAccountsInitial = $company->getBankAccounts()->toArray();

        $form = $this->createForm(CompanyInfoBankAccountType::class);
        $form->get('bank_accounts')->setData($company->getBankAccounts());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (count($form->get('bank_accounts')->getData()) == 0)
            {
                return $this->render('company/edit-bank-accounts.html.twig', [
                    'company' => $company,
                    'user' => $this->getUser(),
                    'form' => $form,
                    'noBankAccounts' => true
                ]);
            }

            $bankAccounts = $form->get('bank_accounts')->getData()->toArray();

            foreach ($bankAccounts as $bankAccount)
            {
                $bankAccount->setCompany($company);
                $company->addBankAccount($bankAccount);
                $em->persist($bankAccount);
            }

            foreach ($bankAccountsInitial as $bankAccount)
            {
                if (!in_array($bankAccount, $bankAccounts))
                {
                    $bankAccount->setCompany(null);
                    $company->removeBankAccount($bankAccount);
                    $em->persist($bankAccount);
                }
            }

            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/edit-bank-accounts.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'form' => $form,
            'noBankAccounts' => false
        ]);
    }

    #[Route('/company/edit/warehouses', name: 'company_edit_warehouses')]
    public function companyEditWarehouses(Request $request, CompanyRepository $companyRepository, TranslatorInterface $translator, WarehouseRepository $warehouseRepository, EntityManagerInterface $em)
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

        $warehousesInitial = $company->getWarehouses()->toArray();

        $form = $this->createForm(CompanyInfoWarehouseType::class);
        $form->get('warehouses')->setData($company->getWarehouses());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $noWarehouses = false;
            $warehouseCodeNotUnique = false;

            if (count($form->get('warehouses')->getData()) == 0)
            {
                $noWarehouses = true;
            } else {
                $warehouseCodes = [];
                foreach ($form->get('warehouses') as $warehouse)
                {
                    $warehouseCodes[] = $warehouse->getData()->getCode();
                    if (count($warehouseCodes) != count(array_unique($warehouseCodes)))
                    {
                        $warehouse->get('code')->addError(new FormError($translator->trans('errors.unique', [], 'app')));
                        $warehouseCodeNotUnique = true;
                    } else {
                        $warehouseCheck = $warehouseRepository->findOneBy(['code' => $warehouse->getData()->getCode()]);
                        if ($warehouseCheck != null && $warehouseCheck->getCompany() != $company) {
                            $warehouse->get('code')->addError(new FormError($translator->trans('errors.unique', [], 'app')));
                            $warehouseCodeNotUnique = true;
                        }
                    }
                }
            }

            if ($noWarehouses || $warehouseCodeNotUnique)
            {
                return $this->render('company/edit-warehouses.html.twig', [
                    'company' => $company,
                    'user' => $this->getUser(),
                    'form' => $form,
                    'noWarehouses' => $noWarehouses
                ]);
            }

            $warehouses = $form->get('warehouses')->getData()->toArray();

            foreach ($warehouses as $warehouse)
            {
                $warehouse->setCompany($company);
                $company->addWarehouse($warehouse);
                $em->persist($warehouse);
            }

            foreach ($warehousesInitial as $warehouse)
            {
                if (!in_array($warehouse, $warehouses))
                {
                    $warehouse->setCompany(null);
                    $company->removeWarehouse($warehouse);
                    $em->persist($warehouse);
                }
            }

            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/edit-warehouses.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'form' => $form,
            'noWarehouses' => false
        ]);
    }

    #[Route('/company/edit/cash-registers', name: 'company_edit_cash_registers')]
    public function companyEditCashRegisters(Request $request, CompanyRepository $companyRepository, EntityManagerInterface $em)
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

        $cashRegistersInitial = $company->getCashRegisters()->toArray();

        $form = $this->createForm(CompanyInfoCashRegisterType::class);
        $form->get('cash_registers')->setData($company->getCashRegisters());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (count($form->get('cash_registers')->getData()) == 0)
            {
                return $this->render('company/edit-cash-registers.html.twig', [
                    'company' => $company,
                    'user' => $this->getUser(),
                    'form' => $form,
                    'noCashRegisters' => true
                ]);
            }

            $cashRegisters = $form->get('cash_registers')->getData()->toArray();

            foreach ($cashRegisters as $cashRegister)
            {
                $cashRegister->setCompany($company);
                $company->addCashRegister($cashRegister);
                $em->persist($cashRegister);
            }

            foreach ($cashRegistersInitial as $cashRegister)
            {
                if (!in_array($cashRegister, $cashRegisters))
                {
                    $cashRegister->setCompany(null);
                    $company->removeCashRegister($cashRegister);
                    $em->persist($cashRegister);
                }
            }

            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_info');
        }

        return $this->render('company/edit-cash-registers.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'form' => $form,
            'noCashRegisters' => false
        ]);
    }
}
