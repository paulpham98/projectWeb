<?php

namespace App\DataFixtures;

use App\Entity\CourseDetail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CourseDetailFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i= 1; $i<= 10; $i++) {
            $course = new CourseDetail;

            $course->setName("Reseach Progaming $i");
            $course->setSlot(rand(25, 40));
            $course->setStart(\DateTime::createFromFormat('Y-m-d', '2022-01-10'));
            $course->setEnd(\DateTime::createFromFormat('Y-m-d', '2022-04-15'));
            $course->setPhoto("cover.jpg");
            $manager->persist($course);
        }
        $manager->flush();
    }
}
