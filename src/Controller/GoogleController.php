<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GoogleController extends AbstractController 
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $entityManager
    ) {        
    }

    #[Route('/connect/google', name:'connect_google')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry->getClient('google')->redirect([], ['email', 'profile']);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(Request $request, ClientRegistry $clientRegistry): Response 
    {
        $client = $clientRegistry->getClient('google');
        try {
            $googleUser = $client->fetchUser();

            // Check if the user exists in your database
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $password = '123456';

            $newUser = new User();
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($existingUser) {
                // Log the user in
                $this->tokenStorage->setToken(
                    new UsernamePasswordToken($existingUser, 'main', $existingUser->getRoles())
                );

                // Redirect dashboard page
                return $this->redirectToRoute('app_dashboard');
            }

            $newUser->setEmail($email);
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setFirstName($name);
            $newUser->setLastName($name);
            // $newUser->setGoogleId($user->getId());
            $hashedPassword = $this->passwordHasher->hashPassword($newUser, $password);
            $newUser->setPassword($hashedPassword);

            $this->entityManager->persist($newUser);
            $this->entityManager->flush();

            // Log the user in
            $this->tokenStorage->setToken(
                new UsernamePasswordToken($newUser, 'main', $newUser->getRoles())
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while logging: ' . $e->getMessage());
            // Redirect login page
            return $this->redirectToRoute('app_login');
        }

        // Redirect dashboard page
        return $this->redirectToRoute('app_dashboard');
    }   
}