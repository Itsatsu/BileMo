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
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;

    }

    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $customerRepository = $manager->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(['name' => 'Customer-' . 1]);
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $user->setFirstName('Prenom' . $i);
            $user->setLastName('Nom' . $i);
            $user->setCustomer($customer);
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }
        $manager->flush();
    }

}
