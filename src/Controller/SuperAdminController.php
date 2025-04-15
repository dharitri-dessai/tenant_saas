<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/super-admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class SuperAdminController extends AbstractController
{
    #[Route('', name: 'app_super_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('super_admin/dashboard.html.twig', [
            'controller_name' => 'SuperAdminController',
        ]);
    }

    #[Route('/users', name: 'app_super_admin_users')]
    public function users(): Response
    {
        // Here you could fetch all users and their roles
        return $this->render('super_admin/users.html.twig');
    }
} 