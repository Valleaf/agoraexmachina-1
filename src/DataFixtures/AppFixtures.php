<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        #Creation d'un compte administrateur par defaut.
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@agora.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin,'agora'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsAllowedEmails(true);


        $manager->persist($admin);
        $manager->flush();
    }
}
