<?php

namespace App\Controller;

use App\Entity\TableSchedule;
use App\Repository\CompanyRepository;
use App\Repository\TableScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TableController extends AbstractController
{
    #[Route('/company/tables', name: 'company_tables')]
    public function tables(CompanyRepository $companyRepository): Response
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

        $tableSchedules = $company->getTableSchedules();

        return $this->render('table/tables.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'table' => $tableSchedules
        ]);
    }

    #[Route('/company/tables/edit/{id}', name: 'company_tables_edit', defaults: ['id' => null])]
    public function edit(TableScheduleRepository $tableScheduleRepository, CompanyRepository $companyRepository, $id): Response
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

        $layout = $tableScheduleRepository->findOneBy(['id' => $id]);

        if ($id != null && ($layout == null || $layout->getCompany() != $company))
        {
            return $this->render('exception/not-found.html.twig');
        }

        return $this->render('table/edit.html.twig', [
            'company' => $company,
            'user' => $this->getUser(),
            'layout' => $layout
        ]);
    }

    #[Route('/company/tables/update/{id}/{layoutId}', name: 'company_tables_update', defaults: ['layoutId' => null])]
    public function update(EntityManagerInterface $em, TableScheduleRepository $tableScheduleRepository, CompanyRepository $companyRepository, $layoutId, $id): Response
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

        $layout = $layoutId ? $tableScheduleRepository->findOneBy(['id' => $layoutId]) : new TableSchedule();

        $layout->setCompany($companyRepository->findOneBy(['id' => $id]));
        $layout->setTitle($_GET['title']);
        $layout->setSchedule($_GET['layout']);

        $em->persist($layout);
        $em->flush();

        return $this->redirectToRoute('company_tables');
    }

    #[Route('/company/tables/remove/{id}', name: 'company_tables_remove')]
    public function remove(EntityManagerInterface $em, TableScheduleRepository $tableScheduleRepository, CompanyRepository $companyRepository, $id): Response
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

        $layout = $tableScheduleRepository->findOneBy(['id' => $id]);

        if ($id != null && ($layout == null || $layout->getCompany() != $company))
        {
            return $this->render('exception/not-found.html.twig');
        }

        $company->removeTableSchedule($layout);

        $em->persist($layout);
        $em->flush();

        return $this->redirectToRoute('company_tables');
    }
}
