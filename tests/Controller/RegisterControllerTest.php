<?php

namespace App\Tests\Controller;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;

class RegisterControllerTest extends AbstractTestController
{
    protected EntityManagerInterface $entityManager;
    protected function setUp(): void
    {
        parent::setUp();
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        if ($em instanceof EntityManagerInterface) {
            $this->entityManager = $em;
        } else {
            $this->fail('Failed to access EntityManager');
        }
    }

    public function testAccessible(): void
    {
        $this->login();
        $this->client->request('GET', '/register');
        $this->client->followRedirect();
        $this->assertResponseRedirects('/profile/informations', 302);
        $this->client->request('GET', '/logout');

        $crawler = $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(200);

        $this->client->submit(
            $crawler->selectButton('Valider')->form(),
            [
                'registration_form[email]' => 'registeruser@test.com',
                'registration_form[plainPassword]' => 'testpassword',
                'registration_form[firstName]' => 'Pierre',
                'registration_form[lastName]' => 'Bondy',
                'registration_form[gender]' => '0',
                'registration_form[_token]' => $crawler->filter('input[name="registration_form[_token]"]')->attr('value'),
                'registration_form[agreeTerms]' => '1'
            ]
        );

        $this->assertResponseRedirects('/profile/informations', 302);

        $userRepository = $this->entityManager->getRepository('App\Entity\Utilisateur');

        if ($userRepository instanceof UtilisateurRepository) {
            $user = $userRepository->findOneBy(['email' => 'registeruser@test.com']);
            if (!is_null($user)) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            } else {
                $this->fail('Failed to get user with email address "registeruser@test.com"');
            }
        } else {
            $this->fail('Failed to get UtilisateurRepository');
        }
    }
}