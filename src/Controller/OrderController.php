<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $orderRepository;
    private $entityManager;
    
    public function __construct(OrderRepository $orderRepository , ManagerRegistry $doctrine) {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $doctrine->getManager();
    }


    

    #[Route('/orders', name: 'orders_list')]
    public function index(): Response
    {   
        $orders = $this->orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }


    #[Route('user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    
    #[Route('/store/order/{product}', name: 'store_order')]
    public function Store(Product $product): Response
    {
        if( !$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $orderExists = $this->orderRepository->findOneBy(['user' => $this->getUser(),
                                                    'pname' => $product->getName()]);

        if ($orderExists) {
            $this->addFlash(
                'warning',
                'Your Have Already Ordered This Product'
             );
             return $this->redirectToRoute('user_order_list');
        }
        $order = new Order();
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('processing...');
        $order->setUser($this->getUser());
        
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->addFlash(
               'success',
               'Your Order Saved Successfully'
            );
            return $this->redirectToRoute('user_order_list');
        
        
    }


    #[Route('/update/order/{order}/{status}', name: 'order_status_update')]
    public function updateOrderStatus(Order $order,$status): Response
    {
        $order->setStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->addFlash(
                    'success',
                    'Your Order Status Updated Successfully'
                    );
        return $this->redirectToRoute('orders_list');
        
    }


    #[Route('/delete/order/{order}', name: 'delete_order')]
    public function deleteOrder(Order $order): Response
    {

        $this->entityManager->remove($order);
        $this->entityManager->flush();
        $this->addFlash(
                    'success',
                    'Your Order Deleted Successfully'
                    );
        return $this->redirectToRoute('orders_list');


    }



}
