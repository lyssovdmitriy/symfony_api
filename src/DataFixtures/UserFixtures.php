<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager,): void
    {
        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.ad');
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, 'admin'));
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setAddress('admin address');
        $userAdmin->setBirthday(new \DateTime('1980-01-25'));
        $userAdmin->setPhone('+12345678910');

        $manager->persist($userAdmin);

        $manager->flush();
    }
}
