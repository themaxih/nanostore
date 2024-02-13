<?php

namespace App\Tests\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractTestController extends WebTestCase
{
    protected KernelBrowser $client;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // Création d'un nouveau client avant chaque test
        $this->client = static::createClient();
    }

    protected function login(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');
        $this->client->request(
            'POST',
            '/login',
            [
                'email' => 'user@test.com',
                'password' => 'testpassword',
                '_csrf_token' => $csrfToken
            ]
        );
    }

    abstract public function testAccessible(): void;
}