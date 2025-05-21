<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ImageForm;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }





    #[Route('/product/create', name: 'app_product_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/product/show/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        $form = $this->createForm(ProductForm::class, $product);
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/product/{id}/delete', name: 'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $manager, Request $request): Response
    {
        if($product){
            $manager->remove($product);
            $manager->flush();
        }
        return $this->redirectToRoute('app_product');
    }



    #[Route('/product/{id}/images', name: 'app_product_image')]
    public function createImages(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
       $image = new Image();
       $form = $this->createForm(ImageForm::class, $image);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $image->setImageProduct($product);
           $manager->persist($image);
           $manager->flush();
           return $this->redirectToRoute('app_product_image', ['id' => $product->getId()]);
       }
       return $this->render('product/createImage.html.twig', [
           'form' => $form->createView(),
           'product' => $product,
       ]);

    }


    #[Route('/product/{id}/images/delete', name: 'app_product_image_delete')]
    public function deleteImage(Image $image, EntityManagerInterface $manager, Request $request): Response
    {

        $product = $image->getProduct();

        $manager->remove($image);
        $manager->flush();
        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }





}
