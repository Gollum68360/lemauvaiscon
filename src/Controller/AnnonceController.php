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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class AnnonceController extends AbstractController
{




    #[Route('/annonces', name: 'app_annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, PaginatorInterface $paginator, Request $request): Response
    {


        $annonces = $paginator->paginate(

            $annonceRepository->findAllNotSold(),
            $request->query->getInt('page', 1), // on récupère le paramètre page en GET. Si le paramètre page n'existe pas dans l'url, la valeur par défaut sera 1
            12 // on veut 12 annonces par page
        );

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
            return $this->redirectToRoute('app_annonce');
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
    public function edit(Annonce $annonce, EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est envoyé et s'il est valide
            $em->flush();
            $this->addFlash('success', 'Annonce modifiée avec succès');
            return $this->redirectToRoute('app_annonce');
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
        return $this->redirectToRoute('app_annonce');
    }
}
