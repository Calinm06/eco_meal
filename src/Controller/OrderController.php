<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Entity\Order;
use App\Entity\Package;
use App\Repository\ConsumerRepository;
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

        $order->setPackageId($package);
        $order->setConsumer($consumer);
        $order->setCreatedAt(new \DateTimeImmutable());

        $entityManager->remove($package);
        $entityManager->flush();

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_package');
    }
}
