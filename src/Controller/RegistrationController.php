<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class RegistrationController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        dump('dddddddd');

        // if ($form->isSubmitted() && !$form->isValid()) {
        //     foreach ($form->getErrors(true) as $error) {
        //         dump($error->getMessage()); // Get the error message
        //         dump($error->getOrigin()?->getName()); // Get the field name where the error occurred (use null-safe operator)
        //     }
        //     //die; // Stop execution to inspect the errors
        // }

        dump($form->isSubmitted());
        dump('fffffffff');
        dump($form->isValid());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                dump('dddddddd1111');
                // Get form data
                $email = $form->get('email')->getData();
                $plainPassword = $form->get('plainPassword')->getData();
                $userType = $form->get('userType')->getData();
                $tenant = $form->get('tenant')->getData();
                $firstname = $form->get('firstname')->getData();
                $lastname = $form->get('lastname')->getData();
                $subdomain = $form->get('subdomain')->getData() ?? '';

                // Create user using the service
                $this->userService->createUser(
                    $email,
                    $plainPassword,
                    $userType,
                    $tenant,
                    $firstname,
                    $lastname,
                    $subdomain
                );

                $this->addFlash('success', 'Your account has been created successfully!');
                return $this->redirectToRoute('app_login');
            } catch (CustomUserMessageAuthenticationException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                dump('dddddddd222222222');
                
                $this->addFlash('error', 'An error occurred during registration. Please try again.'.$e->getMessage());
            }
        }
        dump('dddddddd3333333333');
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
} 