<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@test.com");
        $user->setRoles(['ROLE_ADMIN', 'ROLE_ANALYST']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));

        $manager->persist($user);
        $manager->flush();
    }
}
