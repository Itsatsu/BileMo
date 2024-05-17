<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $customerRepository = $manager->getRepository(Customer::class);

        for ($i = 0; $i < 5; $i++) {
            $customer = $customerRepository->findOneBy(['name' => 'Customer-' . $i]);
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setFirstName('Prenom' . $i);
            $user->setLastName('Nom' . $i);
            $user->setCustomer($customer);
            $manager->persist($user);
        }
        $manager->flush();
    }

}
