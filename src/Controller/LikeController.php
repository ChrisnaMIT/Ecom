<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LikeController extends AbstractController
{
    #[Route('/like/comment/{id}', name: 'app_like_comment')]
    public function index(Comment $comment, LikeRepository $likeRepository, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }


        if($comment->isLikedBy($this->getUser())){
            $like = $likeRepository->findOneBy([
                'comment' => $comment,
                'author' => $this->getUser()
            ]);
            $manager->remove($like);
            $isLiked = false;
        }else{
            $like = new Like();
            $like->setAuthor($this->getUser());
            $like->setComment($comment);
            $manager->persist($like);
            $isLiked = true;
        }
        $manager->flush();

        $data =[
            'isLiked' => $isLiked,
            'count' =>$likeRepository->count(['comment' => $comment]),
        ];
        return $this->json($data, 200);


    }
}
