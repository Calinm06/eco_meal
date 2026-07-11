<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Entity\Order;
use App\Entity\Package;
use App\Repository\ConsumerRepository;
use App\Repository\OrderRepository;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/new/order/{package_id}/{consumer_id}', name: 'app_order_new', methods:['GET','POST'])]
    public function new(int $package_id,int $consumer_id,EntityManagerInterface $entityManager,PackageRepository $packageRepository,ConsumerRepository $consumerRepository): Response
    {
        $order = new Order();

        $package = $packageRepository->find($package_id);
        $consumer = $consumerRepository->find($consumer_id);

        $order->setPackage($package);
        $order->setConsumer($consumer);
        $order->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_package');
    }

    #[Route('/order/delete/{consumer_id}/{order_id}', name: 'app_order_delete')]
    public function delete(int $consumer_id,int $order_id,EntityManagerInterface $entityManager, ConsumerRepository $consumerRepository, OrderRepository $orderRepository)
    {
        $consumer = $consumerRepository->find($consumer_id);
        $order = $orderRepository->find($order_id);

        $entityManager->remove($order);
        $entityManager->flush();

        $orders = $orderRepository->findAll();

        return $this->render('consumer/view.html.twig',[
            'consumer' => $consumer,
            'orders' => $orders
        ]);
    }
}
