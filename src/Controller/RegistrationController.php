<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\CompanyRegistrationType;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register/company', name: 'register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, EntityManagerInterface $em, CompanyRepository $companyRepository, UserRepository $userRepository): Response {
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
                return $this->render('registration/company.html.twig', [
                    'form' => $form->createView(),
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

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/company.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/general/change-password', name: 'change_password')]
    public function changePassword(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, CompanyRepository $companyRepository, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $form = $this->createForm(ChangePasswordType::class);
        $user = $this->getUser();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $error = false;
            $oldPassword = $form->get('old_password')->getData();
            $newPassword = $form->get('password')->getData();

            $passwordNotStartingWithLetter = !ctype_alpha($newPassword[0]);

            if ($passwordNotStartingWithLetter)
            {
                $form->get('password')->get('first')->addError(new FormError($translator->trans('errors.password-regex', [], 'app')));
                $error = true;
            }

            if(!$passwordHasher->isPasswordValid($user, $form->get('old_password')->getData()))
            {
                $form->get('old_password')->addError(new FormError($translator->trans('errors.password-not-correct', [], 'app')));
                $error = true;
            }

            if ($error)
            {
                if ($user->isIsAdmin())
                {
                    return $this->render('admin/change-password.html.twig', [
                        'user' => $user,
                        'form' => $form
                    ]);
                }
                else if ($user->isIsCompany())
                {
                    $company = $companyRepository->findOneBy(['user' => $user]);

                    return $this->render('company/change-password.html.twig', [
                        'user' => $user,
                        'form' => $form,
                        'company' => $company
                    ]);
                }
                else
                {
                    return $this->render('customer/change-password.html.twig', [
                        'user' => $user,
                        'form' => $form
                    ]);
                }
            }

            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_logout');
        }

        if ($user->isIsAdmin())
        {
            return $this->render('admin/change-password.html.twig', [
                'user' => $user,
                'form' => $form
            ]);
        }
        else if ($user->isIsCompany())
        {
            $company = $companyRepository->findOneBy(['user' => $user]);

            return $this->render('company/change-password.html.twig', [
                'user' => $user,
                'form' => $form,
                'company' => $company
            ]);
        }
        else
        {
            return $this->render('customer/change-password.html.twig', [
                'user' => $user,
                'form' => $form
            ]);
        }


    }
}
