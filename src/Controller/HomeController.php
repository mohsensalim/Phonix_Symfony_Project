<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productRepository;
    private $entityManager;
    private $categoryRepository;

    public function __construct(ProductRepository $productRepository , ManagerRegistry $doctrine, CategoryRepository $categoryRepository) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $doctrine->getManager();
    }
    
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $products= $this->productRepository->findAll();
        $categorys= $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,   
            'categorys' => $categorys
        ]);
    }

    #[Route('/product/category/{category}', name: 'app_product_category')]
    public function categoryProducts(Category $category): Response
    {
        $categorys= $this->categoryRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categorys' => $categorys,
        ]);
    }
}
