<?php

namespace App\Controller;

use App\Entity\Tenant;
use App\Entity\User;
use App\Event\TenantCreatedEvent;
use App\Event\TenantUpdatedEvent;
use App\Form\TenantType;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use App\Service\Formatter\DataFormatterManager;
use App\Service\Tenant\TenantServiceSubscriber;

#[Route('/tenant')]
class TenantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/', name: 'app_tenant_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(TenantRepository $tenantRepository, DataFormatterManager $formatterManager): Response
    {
        $sampleData = 'example tenant';
        $formattedData = $formatterManager->formatAll($sampleData);
       
        return $this->render('tenant/index.html.twig', [
            'tenants' => $tenantRepository->findAll(),
            'formattedData' => $formattedData,
        ]);
    }

    #[Route('/new', name: 'app_tenant_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tenant = new Tenant();
        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create a new user for the tenant
            $user = new User();
            $user->setFirstName($tenant->getName());
            $user->setLastName($tenant->getName());
            $user->setEmail($form->get('email')->getData());    
            $user->setRoles(['ROLE_TENANT_ADMIN']);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    '123456'
                )
            );
            $user->setTenant($tenant);
            
            $this->entityManager->persist($user);
            $this->entityManager->persist($tenant);
            $this->entityManager->flush();

            $event = new TenantCreatedEvent($tenant, $this->getUser());
            $this->eventDispatcher->dispatch($event);

            return $this->redirectToRoute('app_tenant_index');
        }

        return $this->render('tenant/new.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tenant_show', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function show(Tenant $tenant): Response
    {
        $tenantAdmin = $tenant->getUsers()->filter(function($user) {
            return in_array('ROLE_TENANT_ADMIN', $user->getRoles());
        })->first();

        return $this->render('tenant/show.html.twig', [
            'tenant' => $tenant,
            'tenantAdminEmail' => $tenantAdmin->getEmail(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tenant_edit', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function edit(Request $request, Tenant $tenant): Response
    {
        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        $tenantAdmin = $tenant->getUsers()->filter(function($user) {
            return in_array('ROLE_TENANT_ADMIN', $user->getRoles());
        })->first();

        dump([
            'name' => $form->get('name')->getData(),
            'subdomain' => $form->get('subdomain')->getData(), 
            'email' => $form->get('email')->getData(),
            'isActive' => $form->get('isActive')->getData()
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldStatus = $tenant->getSubscriptionStatus();

            // Update user details if email changed
            if ($tenantAdmin) {
                $newName= $form->get('name')->getData();
                if ($tenantAdmin->getFirstName() !== $newName) {
                    $tenantAdmin->setFirstName($newName);
                }
                
                $newEmail = $form->get('email')->getData();
                if ($tenantAdmin->getEmail() !== $newEmail) {
                    $tenantAdmin->setEmail($newEmail);
                }
            }
            
            $this->entityManager->flush();

            $event = new TenantUpdatedEvent($tenant, $this->getUser());
            if ($oldStatus !== $tenant->getSubscriptionStatus()) {
                $event->addChange('subscriptionStatus', $oldStatus, $tenant->getSubscriptionStatus())
                      ->setNotify(true);
            }
            $this->eventDispatcher->dispatch($event);

            return $this->redirectToRoute('app_tenant_index');
        }

        return $this->render('tenant/edit.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
            'tenantAdminEmail' => $tenantAdmin->getEmail(),
        ]);
    }

    #[Route('/{id}/subscription', name: 'app_tenant_subscription', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function subscription(Request $request, Tenant $tenant): Response
    {
        return $this->render('tenant/subscription.html.twig', [
            'tenant' => $tenant,
        ]);
    }

    #[Route('/{id}', name: 'app_tenant_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Tenant $tenant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tenant->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tenant);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_tenant_index');
    }

    #[Route('/{id}/process', name: 'app_tenant_process', methods: ['GET'])]
    public function processTenant(Tenant $tenant, TenantServiceSubscriber $subscriber): Response
    {
        // Process tenant-specific data
        $subscriber->processTenantData($tenant->getId());

        return new Response('Tenant data processed successfully.');
    }
} 