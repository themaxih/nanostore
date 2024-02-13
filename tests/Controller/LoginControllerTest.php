<?php

namespace App\Tests\Controller;

use Symfony\Component\BrowserKit\Exception\LogicException;

class LoginControllerTest extends AbstractTestController
{
    public function testAccessible(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(200);

        $this->login();
        $this->assertResponseRedirects('/profile/informations', 302);
    }

    public function testLogout(): void {
        $this->login();
        $this->client->request('GET', '/logout');

        try {
            $this->client->followRedirect();
        } catch (LogicException) {
            $this->fail('Request was not a follow redirect.');
        }

        $this->assertResponseStatusCodeSame(200);
    }
}