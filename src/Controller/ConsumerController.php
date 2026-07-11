<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Form\ConsumerFormType;
use App\Form\RegistrationFormType;
use App\Repository\CategoryRepository;
use App\Repository\ConsumerRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConsumerController extends AbstractController
{
    #[Route('/consumer', name: 'app_consumer')]
    public function index(ConsumerRepository $consumerRepository): Response
    {
        $consumers = $consumerRepository->findAll();
        return $this->render('consumer/index.html.twig', [
            'consumers' => $consumers
        ]);
    }

    #[Route('new/consumer', name:'app_consumer_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consumer = new Consumer();
        $form = $this->createForm(RegistrationFormType::class,$consumer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($consumer);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('registration/register.html.twig',[
            'registrationForm' => $form
        ]);
    }

    #[Route('consumer/{id}/edit', name:'app_consumer_edit', methods:['GET','POST'])]
    public function edit(Request $request,Consumer $consumer, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RegistrationFormType::class,$this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($consumer);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer_view',[
                'id' => $consumer->getId()
            ]);
        }


        return $this->render('registration/register.html.twig',[
            'registrationForm' => $form
        ]);
    }

    #[Route('consumer/{id}', name:'app_consumer_view', methods:['GET'])]
    public function view(Consumer $consumer)
    {
        $orders = $consumer->getOrders();
        return $this->render('consumer/view.html.twig',[
            'consumer' => $consumer,
            'orders' => $orders
        ]);
    }

//    public function show(Consumer $consumer, Security $security)
//    {
//        $this->denyAccessUnlessGranted(['ROLE_CONSUMER','ROLE_ADMIN']);
//        $user = $security->getUser();
//
//        if($user && $user->getConsume)
//    }
}
