<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $productRepository;
    private $entityManager;
    
    public function __construct(ProductRepository $productRepository , ManagerRegistry $doctrine) {
        $this->productRepository = $productRepository;
        $this->entityManager = $doctrine->getManager();
    }


    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $products= $this->productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/store/product', name: 'store_product')]
    public function Store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();
            if($request->files->get('product')['image'])
            {
                $image = $request->files->get('product')['image'];
                $image_name = time()."_".$image->getClientOriginalName();
                $image->move($this->getParameter('image_directory'),$image_name);
                $product->setImage($image_name);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash(
               'success',
               'Product Added Successfully'
            );
            return $this->redirectToRoute('app_product');
        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

     #[Route('/product/{id}/edit', name: 'app_product_edit')]

            public function edit(Request $request, Product $product): Response

                {
                    $form = $this->createForm(ProductType::class, $product);
                    $form->handleRequest($request);
                    if($form->isSubmitted() && $form->isValid())
                    {
                        $product = $form->getData();
                        if($request->files->get('product')['image'])
                        {
                            $image = $request->files->get('product')['image'];
                            $image_name = time()."_".$image->getClientOriginalName();
                            $image->move($this->getParameter('image_directory'),$image_name);
                            $product->setImage($image_name);
                            }
                            $this->entityManager->persist($product);
                            $this->entityManager->flush();
                            $this->addFlash(
                                'success',
                                'Product Updated Successfully'
                                );
                                return $this->redirectToRoute('app_product');
                                }
                                return $this->renderForm('product/edit.html.twig', [
                                    'form' => $form,
                                    ]);
                                    }
    #[Route('/product/{id}/delete', name: 'app_product_delete')]
    public function delete(Request $request, Product $product): Response
    {
        $filesystem = new Filesystem();
        $imagePath = '.uploads/'. $product->getImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }


        $this->entityManager->remove($product);
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Product Removed Successfully'
            );
            return $this->redirectToRoute('app_product');

    }
}
