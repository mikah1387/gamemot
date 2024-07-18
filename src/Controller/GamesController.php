<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Words;
use App\Form\LetterType;
use App\Repository\WordsRepository;
use App\service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/games', name: 'games_')]

class GamesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ApiService $api, EntityManagerInterface $em,
    WordsRepository $wordsRepo): Response
    {    

         $wordsdatas=$wordsRepo->findAll();
        //  dd($wordsdatas);
         if(empty($wordsdatas)){
            $words = $api->fetchWords(5);
            // dd($words);
            foreach ($words as $data) {
              
                   $word = new Words();
                   $worWithoutAcc= $this->removeAccents($data['name']);
                   $word->setWord($worWithoutAcc);
                   if (strlen($worWithoutAcc) >= 5 && strlen($worWithoutAcc) <= 6) {
                       $word->setDifficulty('easy');
                   } elseif (strlen($worWithoutAcc) >= 7 && strlen($worWithoutAcc) <= 8) {
                       $word->setDifficulty('medium');
                   }   elseif (strlen($worWithoutAcc) >= 9 
                   ) {
                       $word->setDifficulty('hard');
                   }
                   $word->setWordlength(strlen($worWithoutAcc));
                   $em->persist($word);
               
               
                  }
                  $em->flush();
                  $this->addFlash('success', 'les mots ont bien été chargés');

         }

         
        return $this->render('games/index.html.twig',['words' => $wordsdatas]);
    }

    #[Route('/{difficultty}', name: 'diff')]
    public function difficultty(WordsRepository $wordsRepo, string $difficultty,
     EntityManagerInterface $em): Response
    {
         $allwords = $wordsRepo->findBy(['difficulty'=>$difficultty] );
         
          $keyword = array_rand($allwords);
          $shooseWord = $allwords[$keyword ];
        //   dd($shooseWord);

          $game = new Games();
          $game->setWord( $shooseWord);
          $game->setAttempts(0);
          $game->setStatus('in_progress');
          $game->setScore(0);
          $game->setUser($this->getUser());
          
          $em->persist($game);
          $em->flush();
          $this->addFlash('success', 'devinez!');
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
     Request $request, EntityManagerInterface $em): Response
    {
            $EnWord = $game->getWord();
            $word = $EnWord->getWord();
            // dd(strlen($word));
            $form = $this->createForm(LetterType::class, null, ['word_length' => strlen($word)]);
             $result= $request->get('resultat');
             $attemptWord=$request->get('attemptWord');
             if (!empty($result))  {
                foreach ($result as $key => $value) {
                
                    if ($value == 'correct'){
                     $letter = $form->get('letter'.$key);
                     $options = $letter->getConfig()->getOptions();
                     $options['attr']['class'] .= ' correct';
                     $options['data'] = $attemptWord[$key];
                     $form->add('letter'.$key, TextType::class, $options);
                    //  $letter->setData($word[$key]);                        
                    }
                    if ($value == 'misplaced'){
                        $letter = $form->get('letter'.$key);
                        $options = $letter->getConfig()->getOptions();
                        $options['attr']['class'] .= ' misplaced';
                        $options['data'] = $attemptWord[$key];
                        $form->add('letter'.$key, TextType::class, $options);
                       //  $letter->setData($word[$key]);                        
                       }
    
                 }
             }

             
            $form->get('letter0')->setData($word[0]);
            $form->get('letter'.strlen($word)-3)->setData($word[strlen($word)-3]);
            if (strlen($word) >= 9) {
                $form->get('letter'.strlen($word)-1)->setData($word[strlen($word)-1]);
            }
            // $form->get('letter0')->setData($word[]);
           

             $form->handleRequest($request);
             
             if ($form->isSubmitted() && $form->isValid()) {
              
             $game->setAttempts($game->getAttempts() + 1);
             $letters = $form->getData();
             $attemptWord = implode('', $letters);
           
             $result = $this->checkWord($attemptWord, $word);
             
          
             if(!in_array('misplaced', $result) && !in_array('incorrect', $result)){
                $game->setStatus('won');
                $game->setScore(1);
                $em-> persist($game);
                $em->flush();
                $this->addFlash('success', 'bravo! vous avez gagné');
                return  $this->redirectToRoute('games_index');
            
             }else if ($game->getAttempts()< 6) {
                // $game->setAttempts($game->getAttempts() + 1);
                $game->setStatus('in_progress');
                $em-> persist($game);
                $em->flush();
                $this->addFlash('alert', ' reessayez !');
  
                return  $this->redirectToRoute('games_show', [
                    'id' => $game->getId(),
                    'resultat' => $result,
                    'attemptWord'=>$attemptWord]);
             }else{
             
                $game->setStatus('lost');
                $em-> persist($game);
                $em->flush();
                $this->addFlash('alert', 'vous avez perdu');
  
                return  $this->redirectToRoute('games_index');
             }
             }
   
        return $this->render('games/show.html.twig', [
            'word' => $word,
            'form' => $form,
            'numbreAttempts' => $game->getAttempts()+1,
        ]);
    }

    private function checkWord(string $attemptWord, string $correctWord): array
    {
     
        $result = [];
        $attemptWord = strtolower($attemptWord);
        $correctWord = strtolower($correctWord);
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
  
    private function removeAccents($string) {

        $string = \Normalizer::normalize($string, \Normalizer::FORM_D);

        // Enlever les diacritiques en gardant les caractères ASCII
        $string = preg_replace('/\p{M}/u', '', $string);
    
        
        return $string;
    }

}
