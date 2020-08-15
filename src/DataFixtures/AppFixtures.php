<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Liior\Faker\Prices;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new Prices($faker));

        for ($i = 0; $i < 10; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->productName)
                ->setDescription($faker->realText(rand(100,2000)))
                ->setPrix($faker->price(20, 200))
                ->setStock($faker->price(0, 2000))
                ->setPhoto('5f353f65d5101.png')
            ;

            $manager->persist($produit);
        }

        $manager->flush();
    }
}
