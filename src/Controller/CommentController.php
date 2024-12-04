<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @method User getUser()
 */
class CommentController extends AbstractController
{
    #[Route('/ajax/comments', name: 'comment_add')]
    public function add(
        Request $request,
        ArticleRepository $articleRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em
    ): Response {
        $commentData = $request->request->all('comment');
    
        // Vérifiez si le token CSRF est valide
        if (!$this->isCsrfTokenValid('comment-add', $commentData['_token'])) {
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'
            ], Response::HTTP_BAD_REQUEST);
        }
    
        // Vérifiez si l'article existe
        $article = $articleRepo->find($commentData['article']);
        if (!$article) {
            return $this->json([
                'code' => 'ARTICLE_NOT_FOUND'
            ], Response::HTTP_BAD_REQUEST);
        }
    
        $user = $this->getUser();

        if(!$user){
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }
        // Créez le nouveau commentaire
        $comment = new Comment($article);
        $comment->setContent($commentData['content']);
        $comment->setUser($user); // Remplacez 1 par l'ID de l'utilisateur connecté
        $comment->setCreatedAt(new \DateTime());
    
        // Persist and flush
        $em->persist($comment);
        $em->flush();
    
        // Récupérez le nombre total de commentaires
        $commentCount = $em->getRepository(Comment::class)->count(['article' => $article]);
    
        // Renvoyez la réponse JSON
        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);
    
        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESSFULLY',
            'message' => $html,
            'numberOfComments' => $commentCount
        ]);
    }
    
}
