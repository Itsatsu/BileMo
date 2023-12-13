<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PhoneFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        $brandRepository = $manager->getRepository(Brand::class);
        $phones = [
            ["Apple", "iPhone 13 Pro", 647, "Bleu,Or,Argent,Gris", 1, 1, 3125, 128, 6.1],
            ["Samsung", "Galaxy A12", 221, "Noir,Blanc,Bleu,Rouge", 0, 1, 5000, 32, 6.5],
            ["Apple", "iPhone 13", 609, "Bleu,Rouge,Rose", 1, 1, 3240, 128, 6.1],
            ["Xiaomi", "MI 12", 405, "Noir,Blanc,Blanc", 1, 1, 4500, 128, 6.28],
            ["Apple", "iPhone 14", 678, "Noir,Blanc,Bleu,Rouge,Violet", 1, 1, 3279, 128, 6.1],
            ["Apple", "iPhone 14 Pro", 1085, "Noir,Or,Argent,Violet", 1, 1, 3200, 128, 6.1],
            ["Google", "Pixel 7", 444, "Noir,Argent,Vert", 1, 1, 4355, 128, 6.3],
            ["Apple", "iPhone 15 Pro", 1064, "Noir,Blanc,Bleu,Argent", 1, 1, 3650, 128, 6.1],
            ["Samsung", "Galaxy S23 FE", 748, "Noir,Blanc,Violet,Marron,Vert", 1, 1, 4500, 128, 6.4],
            ["Xiaomi", "Redmi Note 12 Pro 5G", 247, "Noir,Blanc,Bleu", 1, 1, 5000, 128, 6.67],
            ["Samsung", "Galaxy Z Fold4", 971, "Noir,Bleu,Or,Argent", 1, 1, 4400, 256, 6.2],
            ["Google", "Pixel 5", 235, "Noir,Vert", 1, 1, 4080, 128, 6],
            ["Samsung", "Galaxy S20", 335, "Bleu,Rouge,Rose,Gris", 0, 1, 4000, 128, 6.2],
            ["Samsung", "Galaxy A8", 199, "Noir,Gris,Bleu", 0, 1, 3000, 32, 5.6],
            ["Xiaomi", "Redmi K70E", 366, "Noir,Blanc,Vert", 1, 1, 5500, 256, 6.67],
            ["Xiaomi", "Mi 13", 685, "Noir,Blanc,Bleu", 1, 1, 4500, 256, 6,36],
            ["Xiaomi", "MI 12 Pro", 642, "Noir,Bleu,Violet,Vert", 1, 1, 4600, 256, 6.73],
            ["Google", "Pixel 4", 173, "Noir,Blanc,Orange", 0, 1, 2800, 64, 5.7],
            ["Google", "Pixel 6", 320, "Noir,Vert,Beige", 1, 1, 4600, 128, 6.4],
            ["Apple", "iPhone 15", 799, "Noir,Bleu,Jaune,Rose,Vert", 1, 1, 3877, 128, 6.1],
            ["Google", "Pixel 7a", 409, "Blanc,Gris,Orange,Cyan", 1, 1, 4385, 128, 6.1],
            ["Google", "Pixel 8", 655, "Noir,Cyan,Beige", 1, 1, 4575, 128, 6.2]
        ];

        foreach ($phones as $row) {
            $brand = $brandRepository->findOneBy(['name' => $row[0]]);

            if ($brand == null) {
                $brand = new Brand();
                $brand->setName($row[0]);
                $manager->persist($brand);
                $manager->flush();
            }

            $phone = new Phone();
            $phone->setName($row[1]);
            $phone->setPrice($row[2]);
            $phone->setColor($row[3]);
            $phone->setFiveG($row[4]);
            $phone->setFourG($row[5]);
            $phone->setBattery($row[6]);
            $phone->setStorage($row[7]);
            $phone->setScreenDiagonal($row[8]);
            $phone->setBrand($brand);
            $manager->persist($phone);

        }

        $manager->flush();
    }

}
