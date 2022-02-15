<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/recette')]
class AdminRecetteController extends AbstractController
{
    #[Route('/', name: 'admin_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): Response
    {
        return $this->render('admin_recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $infoImg = $form['img']->getData(); // récupère les informations de l'Img 1
            $extensionImg = $infoImg->guessExtension(); // récupère l'extension de fichier de l'Img 1
            $nomImg = time() . '-1.' . $extensionImg; // reconstitue un nom d'Img unique pour l'Img 1
            $infoImg->move($this->getParameter('recettes_pictures_directory'), $nomImg); // déplace l'Img 1 dans le dossier adéquat
            $recette->setImg($nomImg); // définit le nom de l'image 2 à mettre en base de données

            $manager = $managerRegistry->getManager(); // récupère le manager de Doctrine
            $manager->persist($recette); // dit à Doctrine qu'on va vouloir sauvegarder en bdd
            $manager->flush(); // exécute la requête
            $this->addFlash('success', 'La recette a bien été ajoutée'); // génère un message flash
            return $this->redirectToRoute('admin_recette_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin_recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form, // création de la vue du formulaire et envoi à la vue (fichier)
        ]);
    }

    #[Route('/{id}', name: 'admin_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('admin_recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecetteRepository $recetteRepository, int $id, ManagerRegistry $managerRegistry): Response
    {
        $recette = $recetteRepository->find($id);
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $infoImg = $form['img']->getData();
            $nomOldImg = $recette->getImg(); // récupère le nom de l'ancienne img1
            if ($infoImg !== null) { // vérifie si il y a une img1 dans le formulaire
                $cheminOldImg = $this->getParameter('recettes_pictures_directory') . '/' .$nomOldImg;
                if (file_exists($cheminOldImg)) {
                    unlink($cheminOldImg); // supprime l'ancienne img1
                }
                $nomOldImg = time() . '-1.' . $infoImg->guessExtension(); // reconstitue le nom de la nouvelle img1
                $recette->setImg($nomOldImg); // définit le nom de l'img1 de l'objet Maison
                $infoImg->move($this->getParameter('recettes_pictures_directory'), $nomOldImg); // upload
            } else {
                $recette->setImg($nomOldImg); // re-définit le nom de l'img1 à mettre en bdd
            }
            $manager = $managerRegistry->getManager();
            $manager->persist($recette);
            $manager->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('admin_recette_index');
        }
        return $this->render('admin_recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'admin_recette_delete', methods: ['POST'])]
    public function delete(RecetteRepository $recetteRepository, int $id, ManagerRegistry $managerRegistry): Response
    {
        $recette = $recetteRepository->find($id);
        // throw new \Exception('TODO: gérer la suppression des images du dossier img');
        $img = $this->getParameter('recettes_pictures_directory') . '/' . $recette->getImg();
        if ($recette->getImg() && file_exists($img)) {
            unlink($img);
        }
        $manager = $managerRegistry->getManager();
        $manager->remove($recette);
        $manager->flush();
        $this->addFlash('success', 'Le recette a bien était supprimée');
        return $this->redirectToRoute('admin_recette_index');
    }
}
