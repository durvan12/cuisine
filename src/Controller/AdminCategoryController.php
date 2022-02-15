<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/category')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'admin_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin_category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request,ManagerRegistry $managerRegistry): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $infoImg = $form['img']->getData(); // récupère les informations de l'Img 1
            $extensionImg = $infoImg->guessExtension(); // récupère l'extension de fichier de l'Img 1
            $nomImg = time() . '-1.' . $extensionImg; // reconstitue un nom d'Img unique pour l'Img 1
            $infoImg->move($this->getParameter('recettes_pictures_directory'), $nomImg); // déplace l'Img 1 dans le dossier adéquat
            $category->setImg($nomImg); // définit le nom de l'image 2 à mettre en base de données

            $manager = $managerRegistry->getManager(); // récupère le manager de Doctrine
            $manager->persist($category); // dit à Doctrine qu'on va vouloir sauvegarder en bdd
            $manager->flush(); // exécute la requête
            $this->addFlash('success', 'La recette a bien été ajoutée'); // génère un message flash
            return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin_recette/new.html.twig', [
            'recette' => $category,
            'form' => $form, // création de la vue du formulaire et envoi à la vue (fichier)
        ]);
    }

    #[Route('/{id}', name: 'admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin_category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(CategoryRepository $categoryRepository, int $id, ManagerRegistry $managerRegistry): Response
    {
        $category = $categoryRepository->find($id);
        // throw new \Exception('TODO: gérer la suppression des images du dossier img');
        $img = $this->getParameter('recettes_pictures_directory') . '/' . $category->getImg();
        if ($category->getImg() && file_exists($img)) {
            unlink($img);
        }
        $manager = $managerRegistry->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success', 'La category a bien était supprimée');
        return $this->redirectToRoute('admin_category_index');
    }
}
