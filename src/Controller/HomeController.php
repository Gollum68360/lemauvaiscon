<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findLatestNotSold();

        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenue sur duckzon',
            'content' => 'Lorem ipsum dolor sit amet, consectetur 
            adipisicing elit. Dolorem quam cum corrupti modi cupiditate nostrum odit illo veniam, nulla neque officia expedita rerum, 
            aliquid libero incidunt rem iusto reprehenderit maxime! ',
            'createdAt' => new \DateTime(),
            'annonces' => $annonces
        ]);
    }
}
