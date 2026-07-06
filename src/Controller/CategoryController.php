<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/new', name: 'app_category_new', methods: ['GET','POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request)
    {
        $newCategoy = new Category();
        $form = $this->createForm(CategoryFormType::class,$newCategoy);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($newCategoy);
            $entityManager->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig',[
           'form' => $form
        ]);
    }
}
