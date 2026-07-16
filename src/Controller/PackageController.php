<?php

namespace App\Controller;

use App\Dto\PackageSearchFilter;
use App\Entity\Package;
use App\Form\PackageFilterFormType;
use App\Form\PackageFormType;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/package', name: 'app_package')]
    public function index(Request$request,PackageRepository $packageRepository): Response
    {
        $filter = new PackageSearchFilter();
        $form =  $this->createForm(PackageFilterFormType::class,$filter);
        $form->handleRequest($request);

        return $this->render('package/index.html.twig', [
            'packages' => $packageRepository->findByFilter($filter),
            'form_type' => $form->createView()
        ]);
    }

    #[Route('package/{id}/delete',name: 'app_package_delete')]
    public function delete (Package $package,EntityManagerInterface $entityManager): Response
    {
        $photo = $package->getPhoto();
        if(file_exists($photo)){
            unlink($photo);
        }

        $entityManager->remove($package);
        $entityManager->flush();

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $isBusiness = $this->isGranted('ROLE_BUSINESS');

        if($isAdmin){
            return $this->redirectToRoute('app_package');
        }
        else if($isBusiness){
            return $this->redirectToRoute('app_business_packages',[
                'id' => $package->getBusiness()->getId()
            ]);
        }
        return $this->redirectToRoute('app_package');
    }

    #[Route('package/{id}/edit',name: 'app_package_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Package $package, EntityManagerInterface $entityManager,
                         #[Autowire('%kernel.project_dir%/public/uploads/package')] string $pacakgeDir): Response
    {
        $photo = $package->getPhoto();
        if(file_exists($photo)){
            unlink($photo);
        }

        $form = $this->createForm(PackageFormType::class,$package);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid())
        {
            $photo = $form->get('photo')->getData();
            if($photo){
                $originalFilename = pathinfo($photo->getClientOriginalName(),PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
                $photo->move($pacakgeDir, $newFilename);
                $package->setPhoto('uploads/package/'.$newFilename);

            }

            $entityManager->persist($package);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_packages',[
                'id' => $package->getBusiness()->getId()
            ]);
        }

        return $this->render('package/edit.html.twig',[
            'form' => $form
        ]);
    }

//    #[Route('/package/new',name: 'app_package_new')]
//    public function new(Request $request, EntityManagerInterface $entityManager)
//    {
//        $newPackage = new Package();
//        $form = $this->createForm(PackageFormType::class, $newPackage);
//        $form->handleRequest($request);
//
//        if($form->isSubmitted() && $form->isValid()){
//            $entityManager->persist($newPackage);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_package');
//        }
//
//        return $this->render('package/new.html.twig',[
//            'form' => $form
//        ]);
//    }
}
