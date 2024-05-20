<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\AdminReviewSearchFormType;
use App\Form\ChangePasswordType;
use App\Form\CompanyRegistrationType;
use App\Form\CustomerType;
use App\Repository\BillRepository;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/companies', name: 'admin_all_companies')]
    public function index(UserPasswordHasherInterface $hasher, CompanyRepository $companyRepository): Response
    {
        if(!$this->getUser()->isIsAdmin())
        {
            return $this->render('exception/index.html.twig');
        }

        $activeCompanies = $companyRepository->findBy(['is_active' => 1], ['title' => 'asc']);
        $inactiveCompanies = $companyRepository->findBy(['is_active' => 0, 'editedByAdmin' => 1], ['title' => 'asc']);
        $newCompanies = $companyRepository->findBy(['is_active' => 0, 'editedByAdmin' => 0], ['title' => 'asc']);

        return $this->render('admin/companies.html.twig', [
            'user' => $this->getUser(),
            'activeCompanies' => $activeCompanies,
            'inactiveCompanies' => $inactiveCompanies,
            'newCompanies' => $newCompanies
        ]);
    }

    #[Route('/admin/company/change-status/{companyId}/{status}', name: 'admin_company_change_status')]
    public function changeCompanyStatus(CompanyRepository $companyRepository, EntityManagerInterface $em, $companyId, $status): Response
    {
        if(!$this->getUser()->isIsAdmin())
        {
            return $this->render('exception/index.html.twig');
        }

        $company = $companyRepository->findOneBy(['id' => (int)$companyId]);
        $company->setIsActive($status == "1");
        $company->setEditedByAdmin(true);

        $em->persist($company);
        $em->flush();

        return $this->redirectToRoute('admin_all_companies');
    }

    #[Route('/admin/register/company', name: 'admin_register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, EntityManagerInterface $em, CompanyRepository $companyRepository, UserRepository $userRepository): Response
    {
        if(!$this->getUser()->isIsAdmin())
        {
            return $this->render('exception/index.html.twig');
        }

        $company = new Company();
        $form = $this->createForm(CompanyRegistrationType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // check manually for uniqueness of email and username and if image has correct size
            $passwordNotStartingWithLetter = !ctype_alpha($form->get('password')->getData()[0]);
            $usernameNotUnique = $userRepository->findOneBy(['username' => $form->get('username')->getData()]) != null;
            $emailNotUnique = $companyRepository->findOneBy(['email' => $form->get('email')->getData()]) != null;

            $tmpFile = new File($_FILES['company_registration']['tmp_name']['image']);

            if ($usernameNotUnique)
            {
                $form->get('username')->addError(new FormError($translator->trans('errors.username-not-unique', [], 'app')));
            }

            if ($emailNotUnique)
            {
                $form->get('email')->addError(new FormError($translator->trans('errors.email-not-unique', [], 'app')));
            }

            if ($passwordNotStartingWithLetter)
            {
                $form->get('password')->get('first')->addError(new FormError($translator->trans('errors.password-regex', [], 'app')));
            }

            $imgWidth = getimagesize($tmpFile)[0];
            $imgHeight = getimagesize($tmpFile)[1];

            $incorrectImageSize = $imgWidth < 100 || $imgWidth > 300 || $imgHeight < 100 || $imgHeight > 300;

            if ($incorrectImageSize)
            {
                $form->get('image')->addError(new FormError($translator->trans('errors.image-size', [], 'app')));
            }

            if ($usernameNotUnique || $emailNotUnique || $incorrectImageSize || $passwordNotStartingWithLetter)
            {
                return $this->render('admin/new-company.html.twig', [
                    'form' => $form->createView(),
                    'user' => $this->getUser()
                ]);
            }

            // copy uploaded file to directory that simulates the server

            $extension = explode('/', $_FILES['company_registration']['type']['image'])[1];
            $newFileFullPath = 'server_simulation/' . $form->get('username')->getData() . '.' . $extension;
            copy($tmpFile, $newFileFullPath);

            // create user based on input data

            $user = new User();
            $user->setUsername($form->get('username')->getData());
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setIsCompany(true);
            $user->setIsAdmin(false);

            $company->setUser($user);
            $company->setLogoUrl($newFileFullPath);
            $company->setIsConfigured(false);
            $company->setIsActive(false);
            $company->setEditedByAdmin(false);

            $em->persist($user);
            $em->persist($company);

            $em->flush();

            return $this->redirectToRoute('admin_all_companies');
        }

        return $this->render('admin/new-company.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);
    }

    #[Route('/admin/register/customer', name: 'admin_register_customer')]
    public function registerCustomer(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, TranslatorInterface $translator, UserRepository $userRepository)
    {
        if(!$this->getUser()->isIsAdmin())
        {
            return $this->render('exception/index.html.twig');
        }

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $usernameNotUnique = $userRepository->findOneBy(['username' => $form->get('username')->getData()]) != null;

            if ($usernameNotUnique)
            {
                $form->get('username')->addError(new FormError($translator->trans('errors.username-not-unique', [], 'app')));
                return $this->render('admin/new-customer.html.twig', [
                    'user' => $this->getUser(),
                    'form' => $form->createView()
                ]);
            }

            $user = new User();
            $user->setUsername($form->get('username')->getData());
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setIsCompany(false);
            $user->setIsAdmin(false);

            $customer->setUser($user);

            $em->persist($user);
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('admin_all_companies');
        }

        return $this->render('admin/new-customer.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/review', name: 'admin_review')]
    public function review(Request $request, BillRepository $billRepository)
    {
        if(!$this->getUser()->isIsAdmin())
        {
            return $this->render('exception/index.html.twig');
        }

        $table = [];

        $title = $_GET['title'] ?? '';
        $tin = $_GET['tin'] ?? '';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        if (count($_GET))
        {
            $table = $billRepository->getAllDataForAdminReview($startDate, $endDate, $title, $tin);
        }

        $form = $this->createForm(AdminReviewSearchFormType::class);

        $form->get('title')->setData($title);
        $form->get('tin')->setData($tin);
        $form->get('start_date')->setData($startDate ? new \DateTime($startDate) : null);
        $form->get('end_date')->setData($endDate ? new \DateTime($endDate) : null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $getParameters = $_POST['admin_review_search_form'];

            foreach ($getParameters as $key => $value)
            {
                if (!$getParameters[$key] || $getParameters[$key] == '')
                    unset($getParameters[$key]);
            }

            unset($getParameters['submit']);
            unset($getParameters['_token']);

            return $this->redirectToRoute('admin_review', $getParameters);
        }

        return $this->render('admin/review.html.twig', [
            'user' => $this->getUser(),
            'table' => $table,
            'form' => $form,
            'formSubmitted' => count($_GET)
        ]);
    }

}
