<?php

namespace App\Tests\Controller;

use Symfony\Component\BrowserKit\Exception\LogicException;

class SearchControllerTest extends AbstractTestController
{
    public function testAccessible(): void
    {
        $terms = ['Asus', 'Ordinateur PC', '$*Gamer^&#\'\"'];
        foreach ($terms as $term) {
            $this->client->request('GET', '/search', ['term' => $term]);
            try {
                $this->client->followRedirect();
            } catch (LogicException) {
                // It was not a redirect, skipping
            } finally {
                $this->assertResponseStatusCodeSame(200);
            }
        }

        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
        $this->client->request('GET', '/search', ['term' => '']);
        $this->assertResponseRedirects('/', 302);
    }
}