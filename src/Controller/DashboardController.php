<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TenantRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(
        private TenantRepository $tenantRepository,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }
        
        if ($this->isGranted('ROLE_TENANT_ADMIN')) {
            return $this->redirectToRoute('app_tenant_dashboard');
        }
        
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function adminDashboard(): Response
    {
        $tenants = $this->tenantRepository->findAll();
        $totalUsers = $this->userRepository->count([]);
        $activeTenants = count(array_filter($tenants, fn($tenant) => $tenant->isActive()));
        
        return $this->render('dashboard/admin.html.twig', [
            'tenants' => $tenants,
            'totalUsers' => $totalUsers,
            'activeTenants' => $activeTenants,
            'totalTenants' => count($tenants),
        ]);
    }

    #[Route('/tenant/dashboard', name: 'app_tenant_dashboard')]
    #[IsGranted('ROLE_TENANT_ADMIN')]
    public function tenantDashboard(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tenant = $user->getTenant();
        $users = $this->userRepository->findBy(['tenant' => $tenant]);
        
        return $this->render('dashboard/tenant.html.twig', [
            'tenant' => $tenant,
            'users' => $users,
            'subscription' => $tenant->getSubscription(),
        ]);
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function userDashboard(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tenant = $user->getTenant();
        
        return $this->render('dashboard/user.html.twig', [
            'user' => $user,
            'tenant' => $tenant,
        ]);
    }
} 