<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new Utilisateur();
        $user->setEmail("user@test.com");
        $user->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            'testpassword'
        ));
        $user->setFirstName('Michael');
        $user->setLastName('Schmidt');
        $user->setGender(0);
        $manager->persist($user);
        $manager->flush();
    }
}
