<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('Culture', name: 'culture')]
    public function culture(): Response
    {
        return $this->render('culture.html.twig', [
            'culture' => 'culture',
        ]);
    }

    #[Route('Recette', name: 'recette')]
    public function recette(): Response
    {
        return $this->render('recette.html.twig', [
            'recette' => 'recette',
        ]);
    }

    #[Route('Contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact.html.twig', [
            'contact' => 'contact',
        ]);
    }

    #[Route('Commentaire', name: 'commentaire')]
    public function commentaire(): Response
    {
        return $this->render('commentaire.html.twig', [
            'commentaire' => 'commentaire',
        ]);
    } 
}
