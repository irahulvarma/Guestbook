<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    function testHomepage()
    {
        $client = static::createClient();
        $client->request('GET','/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2','Welcome to Guestbook');
    }

}