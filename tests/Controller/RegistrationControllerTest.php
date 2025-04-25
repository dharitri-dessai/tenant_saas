<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Service\UserService;

class RegistrationControllerTest extends WebTestCase
{
    // public function testRegisterPageLoadsSuccessfully(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/register');

    //     // Assert the page is accessible
    //     $this->assertResponseIsSuccessful();

    //     //Assert form Element is present
    //     $this->assertSelectorExists('form[name="registration_form"]');
    // }

    // public function testRegisterPageWithValidFromValues():void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/register');

    //     $form = $crawler->selectButton('Register')->form();
    //     $form['registration_form[email]'] = 'test5@example.com';
    //     $form['registration_form[plainPassword]'] = '123456';
    //     $form['registration_form[userType]'] = 'tenant';
    //     $form['registration_form[firstname]'] = 'John';
    //     $form['registration_form[lastname]'] = 'Doe';
    //     $form['registration_form[subdomain]'] = 'subdomain5.tenant.saas';
    //     $form['registration_form[tenant]'] = '';
    //     $form['registration_form[agreeTerms]'] = true;

    //     $crawler = $client->submit($form);
    //     //dump($client->getResponse()->getStatusCode()); // Check the HTTP status code
    //     //dump($client->getResponse()->headers->get('Location')); // Check the redirect location, if any
    //     //dump($client->getResponse()->getContent()); // Check the full HTML content of the response
    //     //dump($crawler->filter('title')->text());  

    //     $this->assertResponseRedirects('/login'); //match the status and location
    //     $crawler = $client->followRedirect(); //actual redirect
    //     $this->assertResponseIsSuccessful(); // check if sucessfull 

    //      // Assert the success message on the /login page
    //     $this->assertSelectorTextContains('.alert-success', 'Your account has been created successfully!');
    //     $this->assertSelectorExists('form');

    // }

    // public function testRegisterPageWithInvalidFormValues()
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', '/register');
    //     $form = $crawler->selectButton('Register')->form();

    //     // Intentionally leaving out required fields
    //     $form['registration_form[email]'] = '';

    //     $client->submit($form);
    //     // dump($client->getResponse()->getContent());

    //     $this->assertResponseIsSuccessful();
    
    // }

    // public function testRegisterThrowsCustomException()
    // {
    //     $userServiceMock = $this->createMock(UserService::class);
    //     $userServiceMock->method('createUser')
    //         ->willThrowException(new CustomUserMessageAuthenticationException('There is already an account with this email'));

    //     $client = static::createClient();
    //     self::getContainer()->set(UserService::class, $userServiceMock);

        
    //     $crawler = $client->request('GET', '/register');
    //     $form = $crawler->selectButton('Register')->form();

    //     $form['registration_form[email]'] = 'test@example.com';
    //     $form['registration_form[plainPassword]'] = 'password123';
    //     $form['registration_form[userType]'] = 'tenant';
    //     $form['registration_form[tenant]'] = '';
    //     $form['registration_form[firstname]'] = 'John';
    //     $form['registration_form[lastname]'] = 'Doe';
    //     $form['registration_form[agreeTerms]'] = true;

    //     $client->submit($form);
    //     //dump($client->getResponse()->getContent());

    //     $this->assertSelectorTextContains('li', 'There is already an account with this email');
    // }

    public function testRegisterThrowsUnexpectedException()
    {
        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('createUser')
            ->willThrowException(new \Exception('An error occurred during registration'));

        $client = static::createClient();
        self::getContainer()->set(UserService::class, $userServiceMock);

        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form();

        $form['registration_form[email]'] = 'test99@example.com';
        $form['registration_form[plainPassword]'] = 'password123';
        $form['registration_form[userType]'] = 'tenant';
        $form['registration_form[tenant]'] = '';
        $form['registration_form[firstname]'] = 'John';
        $form['registration_form[lastname]'] = 'Doe';
        $form['registration_form[agreeTerms]'] = true;
        $form['registration_form[subdomain]'] = 'subdomain99.tenant.saas';

        $client->submit($form);

        dump($client->getResponse()->getContent());
        
        $this->assertSelectorTextContains('li', 'An error occurred during registration');
    }

}

