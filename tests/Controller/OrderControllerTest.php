<?php

namespace App\Tests\Controller;

class OrderControllerTest extends AbstractTestController
{
    public function testAccessible(): void
    {
        // Tentative d'accès à la page de paiement en n'étant pas connecté
        $this->client->request('GET', '/order');
        //Redirection vers la page de login attendu
        $this->assertResponseRedirects('/login', 302);

        // 2ème tentative cette fois en se connectant
        $this->login();
        $this->client->request('GET', '/order');
        // Si le panier est vide alors on est redirigé vers le panier !
        $this->assertResponseRedirects('/cart', 302);

        // On ajoute un produit au panier
        $this->client->request('GET', "/cart/add-to-cart?id=1");

        $this->assertResponseRedirects('/cart/cart-gateway?ref=Xiaomi%20Redmi%2012%20Argent%20%288%20Go%20/%20256%20Go%29', 302);

        // On tente d'accéder à la page de paiement
        $this->client->request('GET', '/order');
        $this->assertResponseStatusCodeSame(200); // Tout devrait fonctionner

        // On vide le panier
        $this->client->request('POST', '/cart/empty-cart');

        // On vérifie qu'on est bien redirigé après le vidage du panier
        $this->assertResponseRedirects('/cart', 302);
    }
}