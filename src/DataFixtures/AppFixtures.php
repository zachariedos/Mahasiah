<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface  $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin
            ->setEmail('admin@admin.fr')
            ->setFirstName('admin')
            ->setLastName('admin')
            ->setAddress('14, Rue des Admins')
            ->setPostalCode('21000')
            ->setCity('Dijon')
            ->setRoles(array('ROLE_ADMIN'))
            ->setPassword($this->encoder->encodePassword($admin, 'AdminPass+21'));
        $manager->persist($admin);

        // création d'un compte User pour test
        $user = new User();
        $user
            ->setEmail('user@user.fr')
            ->setFirstName('user')
            ->setLastName('user')
            ->setAddress('14, Rue des users')
            ->setPostalCode('21000')
            ->setCity('Dijon')
            ->setPassword($this->encoder->encodePassword($admin, 'UserPass+21'));
        $manager->persist($user);
    }
}
