<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i= 1; $i<= 10; $i++) {
            $category = new Category;

            $category->setName("IT $i");
            $category->setCode("GCH0805");
            $category->setDescription("Description for category $i");
            $category->setImage("cover.jpg");
            $manager->persist($category);
        }

        $manager->flush();
    }
}
