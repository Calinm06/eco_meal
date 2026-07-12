<?php

namespace App\Controller;

use App\Dto\ConsumerEdit;
use App\Entity\Consumer;
use App\Entity\User;
use App\Form\ConsumerEditFormType;
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
    public function edit(Request $request,Consumer $consumer, User $user,EntityManagerInterface $entityManager): Response
    {
        $consumerEdit = new ConsumerEdit();
        $consumerEdit->setFirstName($consumer->getFirstName());
        $consumerEdit->setLastName($consumer->getLastName());
        $consumerEdit->setPhoneNumber($consumer->getPhoneNumber());
        $consumerEdit->setEmail($user->getEmail());

        $form = $this->createForm(ConsumerEditFormType::class,$consumerEdit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $consumer->setFirstName($consumerEdit->getFirstName());
            $consumer->setLastName($consumerEdit->getLastName());
            $consumer->setPhoneNumber($consumerEdit->getPhoneNumber());
            $user->setEmail($consumerEdit->getEmail());

            $entityManager->flush();

            return $this->redirectToRoute('app_consumer_view',[
                'id' => $consumer->getId()
            ]);
        }


        return $this->render('consumer/edit.html.twig',[
            'form' => $form
        ]);
    }

    #[Route('consumer/{id}', name:'app_consumer_view', methods:['GET'])]
    public function view(Consumer $consumer) : Response
    {
        $orders = $consumer->getOrders();
        $packages = [];
        foreach($orders as $order){
            $packages[] = $order->getPackageId();
        }
        return $this->render('consumer/view.html.twig',[
            'consumer' => $consumer,
            'packages' => $packages
        ]);
    }

    #[Route('consumer/{id}/delete', name: 'app_consumer_delete')]
    public function delete(Consumer $consumer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($consumer);
        $entityManager->flush();

       return $this->redirectToRoute('app_consumer');
    }
//    public function show(Consumer $consumer, Security $security)
//    {
//        $this->denyAccessUnlessGranted(['ROLE_CONSUMER','ROLE_ADMIN']);
//        $user = $security->getUser();
//
//        if($user && $user->getConsume)
//    }
}
