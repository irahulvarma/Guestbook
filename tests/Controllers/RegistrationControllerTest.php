<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    function testRegistrationpage()
    {
        $client = static::createClient();
        $client->request('GET','/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html','Registration Form');
    }

    function testRegistration()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $email = 'guest'.date('YmdHis').'@guestbook.com';
        $data = array('registration_form[email]' => $email,'registration_form[plainPassword]' => 'Admin@123');
        $client->submit($form,$data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', 'Add Guestbook');
    }

}