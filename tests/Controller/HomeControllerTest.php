<?php

namespace App\Tests\Controller;

use App\Repository\UtilisateurNewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;

class HomeControllerTest extends AbstractTestController
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
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);

        // Vérifier la présence du cookie si c'est un nouvel utilisateur
        if (!$this->client->getCookieJar()->get('dejaVisite')) {
            $cookie = $this->client->getCookieJar()->get('dejaVisite');
            $this->assertNotNull($cookie);
            $this->assertEquals('1', $cookie->getValue());
        }
    }

    const userEmail = 'user@test.com';
    const aboReussis = 'Votre abonnement à la newsletter à bien été pris en compte.';
    const dejaPresent = 'Il semble que vous soyez déjà abonné à notre newsletter.';

    public function testNewsletter(): void
    {

        $this->client->request('POST', '/subscribe-newsletter', [
            'email' => self::userEmail,
            'current-url' => '/'
        ]);
        $this->assertResponseRedirects('/', 302);
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString(self::aboReussis, $crawler->html());
        $this->client->request('POST', '/subscribe-newsletter', [
            'email' => self::userEmail,
            'current-url' => '/'
        ]);

        $this->assertResponseRedirects('/', 302);
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString(self::dejaPresent, $crawler->html());


        $unRepository = $this->entityManager->getRepository('App\Entity\UtilisateurNewsletter');

        if ($unRepository instanceof UtilisateurNewsletterRepository) {
            $user = $unRepository->findOneBy(['email' => self::userEmail]);
            if (!is_null($user)) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            } else {
                $this->fail('Failed to get email address "user@test.com"');
            }
        } else {
            $this->fail('Failed to get UtilisateurNewsletterRepository');
        }
    }
}