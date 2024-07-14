<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Words;
use App\Form\LetterType;
use App\Repository\WordsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/games', name: 'games_')]

class GamesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {


        return $this->render('games/index.html.twig', [
            'controller_name' => 'GamesController',
        ]);
    }

    #[Route('/{difficultty}', name: 'diff')]
    public function difficultty(WordsRepository $wordsRepo, string $difficultty,
     Request $request,
     EntityManagerInterface $em): Response
    {
         $allwords = $wordsRepo->findBy(['difficulty'=>$difficultty] );
         
          $keyword = array_rand($allwords);
          $shooseWord = $allwords[$keyword ];
        //   dd($shooseWord);
          
          
        //   $keyword = array_rand($words);
        //   $wordValue = $words[$keyword ];

          $game = new Games();
          $game->setWord( $shooseWord);
          $game->setAttempts(0);
          $game->setStatus('in_progress');
          $game->setScore(0);
          $game->setUser($this->getUser());
          
          $em->persist($game);
          $em->flush();
          $this->addFlash('success', 'Game Started');
          return  $this->redirectToRoute('games_show', ['id' => $game->getId()]);
        
        //   $form = $this->createForm(LetterType::class, null, ['word_length' => strlen($wordValue)]);
        //   $form->handleRequest($request);
        //   $form['letter0']->setData($wordValue[0]);
        //   if ($form->isSubmitted() && $form->isValid()) {
        //     $letters = $form->getData();
        //     $attemptWord = implode('', $letters);
        //     dd( $attemptWord);
        //     // $result = $this->checkWord($attemptWord, $wordValue);
        //   }
       
        return $this->render('games/diff.html.twig', [
            'word' => $shooseWord
           ]);
        
    }

    #[Route('/game/{id}', name: 'show')]
    public function startGame(Games $game,
     Request $request): Response
    {
            $EnWord = $game->getWord();
            $word = $EnWord->getWord();
            $form = $this->createForm(LetterType::class, null, ['word_length' => strlen($word)]);
            $form['letter0']->setData($word[0]);

             $form->handleRequest($request);
             
             if ($form->isSubmitted() && $form->isValid()) {

            $letters = $form->getData();
            $attemptWord = implode('', $letters);
           
            $result = $this->checkWord($attemptWord, $word);
            dd($result);
          }
   
        return $this->render('games/show.html.twig', [
            'word' => $word,
            'form' => $form
        ]);
    }

    private function checkWord(string $attemptWord, string $correctWord): array
    {
     
        $result = [];
        for ($i = 0; $i < strlen($attemptWord); $i++) {
            if ($attemptWord[$i] === $correctWord[$i]) {
                $result[] = 'correct';
            } elseif (strpos($correctWord, $attemptWord[$i]) !== false) {
                $result[] = 'misplaced';
            } else {
                $result[] = 'incorrect';
            }
        }
        return $result;
    }
}
