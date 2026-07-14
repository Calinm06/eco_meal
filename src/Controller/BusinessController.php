<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Package;
use App\Form\BusinessFormType;
use App\Form\PackageFormType;
use App\Repository\BusinessRepository;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BusinessController extends AbstractController
{
    #[Route('/business', name: 'app_business')]
    public function index(BusinessRepository $businessRepository): Response
    {
        $businesses = $businessRepository->findAll();

        return $this->render('business/index.html.twig', [
            'businesses' => $businesses,
        ]);
    }

    #[Route('/business/{id}', name: 'app_business_view')]
    public function view(Business $business): Response
    {
        return $this->render('business/view.html.twig', [
            'business' => $business,
        ]);
    }

    #[Route('/new/business', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function newBusiness(Request $request,EntityManagerInterface $entityManager): Response
    {
        $business = new Business();
        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($business);
            $entityManager->flush();

            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/new.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/business/{id}/new', name:'app_package_new', methods: ['GET','POST'])]
    public function newPackage(Business $business, Request $request,EntityManagerInterface $entityManager,
                               #[Autowire('%kernel.project_dir%/public/uploads/package')] string $pacakgeDir): Response
    {


        $package = new Package();
        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();
            if($photo){
                $originalFilename = pathinfo($photo->getClientOriginalName(),PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
                $photo->move($pacakgeDir, $newFilename);
                $package->setPhoto('uploads/package/'.$newFilename);

            }



            $package->setBusiness($business);
            $entityManager->persist($package);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_view',[
                'id' => $business->getId()
            ]);
        }
        return $this->render('package/new.html.twig',[
            'form' => $form,
            'business' => $business
        ]);
    }
    #[Route('business/{id}/statistics',name: 'app_business_statistics')]
    public function statistics(Business $business, BusinessRepository $businessRepository)
    {
        $totalPackages = $business->getPackages()->count();
        $numberOfOrders = $businessRepository->getNumberOfOrders($business);
        $totalSum = $businessRepository->getTotalSum($business);
        $mostBoughtCategory = $businessRepository->getMostBoughtCategory($business)[0]->getPackageId()->getCategory();
        $orders = $businessRepository->getLastDaysOrders($business);

       // dd($mostBoughtCategory);
        return $this->render('business/statistics.html.twig',[
            'totalPackages' => $totalPackages,
            'numberOfOrders' => $numberOfOrders,
            'totalSum' => $totalSum,
            'categories' => $mostBoughtCategory,
            'orders' => $orders
        ]);
    }
}
