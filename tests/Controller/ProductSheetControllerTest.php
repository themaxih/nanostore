<?php

namespace App\Tests\Controller;

class ProductSheetControllerTest extends AbstractTestController
{
    public function testAccessible(): void
    {
        $crawler = $this->client->request('GET', '/search', ['term' => 'Asus']);
        $this->assertResponseStatusCodeSame(200);

        preg_match_all('#<a class="tr product pointer" href="/product\?ref=([^"]+)"#', $crawler->html(), $allHref);

        foreach ($allHref[1] as $href) {
            $this->client->request('GET', "/product?ref=$href");
            $this->assertResponseStatusCodeSame(200);
        }
    }
}