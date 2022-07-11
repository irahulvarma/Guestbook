<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    function testLoginpage()
    {
        $client = static::createClient();
        $client->request('GET','/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html','Please sign in');
    }

    function testAdminLogin()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $data = array('email' => 'staff@guestbook.com','password' => 'Admin@123');
        $client->submit($form,$data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Admin List Page');
    }

    function testGuestLogin()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $data = array('email' => 'guest1@guestbook.com','password' => 'Admin@123');
        $client->submit($form,$data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', 'Add Guestbook');
    }

}