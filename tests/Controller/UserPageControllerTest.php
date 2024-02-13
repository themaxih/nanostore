<?php

namespace App\Tests\Controller;

class UserPageControllerTest extends AbstractTestController
{

    public function testAccessible(): void
    {
        $this->client->request('GET', '/profile');
        $this->assertResponseRedirects('/login', 302);

        $this->client->request('GET', '/profile/informations');
        $this->assertResponseRedirects('/login', 302);

        $this->client->request('GET', '/profile/orders');
        $this->assertResponseRedirects('/login', 302);

        $this->login();
        $this->client->request('GET','/profile');
        $this->assertResponseRedirects('/profile/informations', 302);

        $this->client->request('GET', '/profile/orders');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPasswordChange(): void
    {
        $this->login(); // Connexion

        // On vérifie que l'on ne peut pas accéder à cette adresse en GET
        $this->client->request('GET', '/profile/change-password');
        $this->assertResponseStatusCodeSame(404);

        // Changement du mot de passe
        $this->changePassword('testpassword', 'testChangingPassword');
        $this->assertResponseRedirects('/profile/informations', 302);
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('Votre mot de passe à bien été modifié.', $crawler->html());
        $this->changePassword('testChangingPassword', 'testpassword');

        // Mot de passe actuelle incorrect
        $this->changePassword('wrongPassword', 'testChangingPassword');
        $this->assertResponseRedirects('/profile/informations', 302);
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('Le mot de passe actuel est incorrect, veuillez réessayer.', $crawler->html());

        // Nouveau et ancien mot de passe identique
        $this->changePassword('testpassword', 'testpassword');
        $this->assertResponseRedirects('/profile/informations', 302);
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString("Le nouveau mot de passe est identique à l'ancien, veuillez choisir un autre mot de passe.", $crawler->html());
    }

    private function changePassword(string $oldPassword, string $newPassword): void
    {
        $this->client->request(
            'POST',
            '/profile/change-password',
            ['old-password' => $oldPassword, 'new-password' => $newPassword]
        );
    }
}