<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        dump($form);
        $name = $form->get('name')->getData();
        $description = $form->get('description')->getData();
        $price = $form->get('price')->getData();
        $category = $form->get('category')->getData();
        $stock = $form->get('size')->getData();
        $sku = $form->get('location')->getData();
        $createdAt = $form->get('color')->getData();
        $updatedAt = $form->get('releaseDate')->getData();


        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $formData = $form->getData();
                dump($formData);
                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Product created successfully!');
                return $this->redirectToRoute('app_product_new');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating the product: ' . $e->getMessage());
            }
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
