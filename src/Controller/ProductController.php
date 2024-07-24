<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }


    #[Route('/store/product', name: 'store_product')]
    public function Store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }
}
