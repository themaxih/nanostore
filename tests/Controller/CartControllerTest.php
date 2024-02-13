<?php

namespace App\Tests\Controller;

class CartControllerTest extends AbstractTestController
{
    public function testAccessible(): void
    {
        // Tentative d'accès au panier en n'étant pas connecté
        $this->client->request('GET', '/cart');
        //Redirection vers la page de login attendu
        $this->assertResponseRedirects('/login', 302);

        // 2ème tentative cette fois en se connectant
        $this->login();

        $this->client->request('GET', "/cart/add-to-cart?id=1");
        $this->assertResponseRedirects('/cart/cart-gateway?ref=Xiaomi%20Redmi%2012%20Argent%20%288%20Go%20/%20256%20Go%29', 302);

        $crawler = $this->client->request('GET', '/cart');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Supprimer')->form();
        $this->client->submit($form);
    }
}
