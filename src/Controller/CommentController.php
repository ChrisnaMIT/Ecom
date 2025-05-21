<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_comment')]
    public function comment(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentForm::class , $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setProduct($product);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTime());
            $manager->persist($comment);
            $manager->flush();
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/comment/{id}/delete', name: 'app_comment_delete')]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $product = $comment->getProduct();
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);

    }




}
