<?php

namespace App\Controller;

use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(  GamesRepository $gramesRepo,): Response
    {
         $user = $this->getUser();
        
         $numberGames = $gramesRepo->countByUser($user);
   
         $numberGamesWon=$gramesRepo->findBy([
             'user'=>$user, 'status'=>'won']);
        
         $numberGamesLost=$gramesRepo->findBy([
            'user'=>$user, 'status'=>'lost']);
         $numberGamesNotFinished =$gramesRepo->findBy([
                'user'=>$user, 'status'=>'in_progress']);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'numberGames' => $numberGames,
            'numberGamesWon' =>  $numberGamesWon,
            'numberGamesLost' => $numberGamesLost,
            'numberGamesNotFinished' => $numberGamesNotFinished,
        ]);
    }
}
