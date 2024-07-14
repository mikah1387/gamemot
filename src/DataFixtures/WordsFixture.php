<?php

namespace App\DataFixtures;

use App\Entity\Words;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WordsFixture extends Fixture
{
    public function load(ObjectManager $manager): void          
    {

        $words = [
            ['word' => 'apple', 'difficulty' => 'easy'],
            ['word' => 'banana', 'difficulty' => 'easy'],
            ['word' => 'grape', 'difficulty' => 'easy'],
            ['word' => 'orange', 'difficulty' => 'easy'],
            ['word' => 'pineapple', 'difficulty' => 'medium'],
            ['word' => 'grapefruit', 'difficulty' => 'medium'],
            ['word' => 'pomegranate', 'difficulty' => 'medium'],
            ['word' => 'persimmon', 'difficulty' => 'hard'],
            ['word' => 'blackberry', 'difficulty' => 'hard'],
        ];
        foreach ($words as $data) {
            $word = new Words();
            $word->setWord($data['word']);
            $word->setDifficulty($data['difficulty']);
            $manager->persist($word);
        }
        $manager->flush();
    }
}