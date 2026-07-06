<?php

namespace App\Controller;

use App\Entity\Package;
use App\Form\PackageFormType;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/package', name: 'app_package')]
    public function index(PackageRepository $packageRepository): Response
    {
        $packages = $packageRepository->findAll();
        return $this->render('package/index.html.twig', [
            'packages' => $packages
        ]);
    }

    #[Route('/package/new',name: 'app_package_new')]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $newPackage = new Package();
        $form = $this->createForm(PackageFormType::class, $newPackage);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($newPackage);
            $entityManager->flush();

            return $this->redirectToRoute('app_package');
        }

        return $this->render('package/new.html.twig',[
            'form' => $form
        ]);
    }
}
