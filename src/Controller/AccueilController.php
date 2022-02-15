<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecetteRepository;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(RecetteRepository $RecetteRepository): Response
    {
        return $this->render('accueil/index.html.twig', [
            'mekla' => $RecetteRepository->findBy(
            array(),
            array('id'=> 'ASC'),
            4,
            0
            )    
        ]);
    }

}
