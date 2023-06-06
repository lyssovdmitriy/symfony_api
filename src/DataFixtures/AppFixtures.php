<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager,): void
    {
        $userAdmin = new User();
        $userAdmin->setEmail('admin');
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, 'admin'));
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setAddress('admin address');
        $userAdmin->setBirthday(new \DateTime('1980-01-25'));
        $userAdmin->setPhone('+12345678910');

        $manager->persist($userAdmin);

        $user = new User();
        $user->setEmail('user');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        $user->setAddress('user address');
        $user->setBirthday(new \DateTime('1980-01-25'));
        $user->setPhone('+12345678910');

        $manager->persist($user);

        $application = new Application();
        $application->setDate(new DateTime());
        $application->setNumber('1234567878joifjew90');
        $application->setTitle('Application test 1');
        $application->setUser($user);
        $application->setUpdatedAt(new DateTimeImmutable());

        $manager->persist($application);

        $application = new Application();
        $application->setDate(new DateTime());
        $application->setNumber('425fg34t34');
        $application->setTitle('Application test 2');
        $application->setUser($user);
        $application->setUpdatedAt(new DateTimeImmutable());

        $manager->persist($application);

        $manager->flush();
    }
}
