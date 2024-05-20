<?php

namespace App\Controller;

use App\Repository\BillRepository;
use ContainerXQhQqEd\getCache_SecurityIsGrantedAttributeExpressionLanguageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, BillRepository $billRepository): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        $allBills = $billRepository->findAll();
        $bills = $allBills;
        if (count($allBills) > 5)
        {
            $bills = array_slice($allBills, count($allBills) - 5, 5);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'bills' => $bills]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/determine/user', name: 'determine_user')]
    public function determineUser()
    {
        $user = $this->getUser();
        if ($user->isIsAdmin())
        {
            return $this->redirectToRoute('admin_all_companies');
        }
        else if ($user->isIsCompany())
        {
            return $this->redirectToRoute('company');
        }
        else
        {
            return $this->redirectToRoute('customer_all_companies');
        }
    }
}
