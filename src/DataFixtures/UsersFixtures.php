<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

         for ($i=0; $i < 10; $i++) { 
          
            $user = new User();
            $user->setPseudo('pseudo'.$i);
            $user->setPassword('123456');
            $user->setRoles(['ROLE_USER']);
            $user->setNumberSecu('123456789');
            $manager->persist($user);

         }
      
        
        $manager->flush();
    }
}
