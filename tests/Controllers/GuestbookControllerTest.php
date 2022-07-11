<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class GuestbookControllerTest extends WebTestCase
{
    function testGuestbookAddPage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('guest1@guestbook.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/guestbook/add');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body','Add Guestbook');
    }

    function testGuestbookView()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('staff@guestbook.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/guestbook/view/1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body','View Guestbook');
    }
}