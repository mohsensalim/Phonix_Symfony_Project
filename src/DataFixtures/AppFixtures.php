<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hashedPassword;

    public function __construct(UserPasswordHasherInterface $hashedPassword) {

            $this->hashedPassword = $hashedPassword;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $plainpassword = "12345678";
        $hashedPassword = $this->hashedPassword->hashPassword($user,$plainpassword);
        $user->setUsername("admin");
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
         $manager->persist($user);
        
        $manager->flush();
    }
}
