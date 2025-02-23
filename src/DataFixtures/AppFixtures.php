<?php

namespace App\DataFixtures;

use App\Entity\Topic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $topics = ['Education', 'Quiz', 'Other'];
        foreach ($topics as $topicName) {
            $topic = new Topic();
            $topic->setName($topicName);
            $manager->persist($topic);
        }

        $manager->flush();
    }
}
