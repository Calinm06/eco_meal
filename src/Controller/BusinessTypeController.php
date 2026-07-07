<?php

namespace App\Controller;

use App\Entity\BusinessType;
use App\Form\BusinessTypeFormType;
use App\Repository\BusinessTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BusinessTypeController extends AbstractController
{
    #[Route('/businessType', name: 'app_business_type')]
    public function index(BusinessTypeRepository $businessTypeRepository): Response
    {
        $types = $businessTypeRepository->findAll();
        return $this->render('business_type/index.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('businessType/new', name: 'app_business_type_new')]
    public function new(Request $request,EntityManagerInterface $entityManager)
    {
        $newType = new BusinessType();
        $form = $this->createForm(BusinessTypeFormType::class,$newType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($newType);
            $entityManager->flush();
            return $this->redirectToRoute('app_business_type');
        }

        return $this->render('business_type/new.html.twig',[
           'form' => $form
        ]);
    }

    #[Route('businessType/{id}', name: 'app_business_type_show')]
    public function show(BusinessTypeRepository $businessTypeRepository, int $id): Response
    {
        $type = $businessTypeRepository->find($id);
        return $this->render('business_type/view.html.twig',[
            'type' =>$type,
        ]);
    }

    #[Route('businessType/{id}/edit', name: 'app_business_type_edit',methods: ['GET','POST'])]
    public function edit(Request $request, BusinessType $businessType, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(BusinessTypeFormType::class,$businessType);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            return $this->redirectToRoute('app_business_type');
        }

        return $this->render('business_type/update.html.twig',[
            'form' => $form
        ]);
    }

    #[Route('businessType/{id}/delete', name: 'app_business_type_delete', methods: ['GET','DELETE'])]
    public function delete(BusinessType $businessType, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($businessType);
        $entityManager->flush();

        return $this->redirectToRoute('app_business_type');
    }
}
