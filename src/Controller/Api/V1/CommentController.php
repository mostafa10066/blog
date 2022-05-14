<?php


namespace App\Controller\Api\V1;


use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Cmf\Api\Slugifier\CallbackSlugifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;


class CommentController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }

    /**
     * @Rest\Get("/articles/{id}/comments")
     * @ParamConverter("article", class = "App\Entity\Article")
     * @param Article $article
     * @return Response
     */
    public function show(Article $article){
        $comments=$article->getComments();
        $view = $this->view(array('data'=>$comments));
        $view->getContext()->setGroups(array(
            'comment_list',
        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }
    /**
     * Creates a user
     * @Rest\Post("/articles/{id}/comments")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function create(Article $article, Request $request){
        $comment= new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters['data']);
        if ($form->isValid()) {
            $comment=$form->getData();
            $article->addComment($comment);
            $this->em->persist($article);
            $this->em->flush();
            $view = $this->view(array('data'=>$comment),201);
            $view->getContext()->setGroups(array(
                'comment_list',
            ));
            $view->getContext()->setSerializeNull(true);
            return $this->handleView($view);
        }
        else{
            $errors=$this->view( $form, Response::HTTP_BAD_REQUEST);
            return $this->handleView($errors);
        }
    }



}