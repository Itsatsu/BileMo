<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;

    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $customer = new Customer();
            $customer->setName('Customer-' . $i);
            $customer->setEmail('customer' . $i . '@gmail.com');
            $customer->setPassword($this->userPasswordHasher->hashPassword($customer, 'password'));
            $customer->setRoles(['ROLE_USER']);
            $manager->persist($customer);
        }
        $manager->flush();
    }

}
