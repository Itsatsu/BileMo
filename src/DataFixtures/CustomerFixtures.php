<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\BrandRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class CustomerFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 5; $i++) {
            $customer = new Customer();
            $customer->setName('Customer-' . $i);
            $manager->persist($customer);
        }
        $manager->flush();
    }

}
