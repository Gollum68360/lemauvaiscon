<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AnnonceController extends AbstractController
{




    #[Route('/annonces', name: 'app_annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {

        $annonces = $annonceRepository->findAllNotSold();

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces,
        ]);
    }

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em)
    {
        $annonce = new Annonce();

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('app_annonce_index');
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }

    #[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est envoyé et s'il est valide
            $em->flush();
            //return $this->redirectToRoute('app_annonce_edit', ['id' => $annonce->getId()]);
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'formView' => $form->createView()
        ]);
    }

    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Annonce $annonce, EntityManagerInterface $em, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->get('_token'))) {
            $em->remove($annonce);
            $em->flush();
        }
        return $this->redirectToRoute('app_annonce_index');
    }
}
