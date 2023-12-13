<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $customer = new Customer();
            $customer->setName('Customer-' . $i);
            $manager->persist($customer);
        }
        $manager->flush();
    }

}
