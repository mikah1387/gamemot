<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(UserRepository $UsersRepo): Response
    {

        return $this->render('main/index.html.twig', [
            'users' => $UsersRepo->findAll(),
        ]);
    }
}
