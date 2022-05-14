<?php


namespace App\Controller\Api\V1;


use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
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


class ArticleController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger,EntityManagerInterface $em){
        $this->em=$em;
        $this->slugger=$slugger;
    }

    /**
     * @Rest\Get("/articles/{id}")
     * @ParamConverter("article", class = "App\Entity\Article")
     * @param Article $article
     * @return Response
     */
    public function show(Article $article){
        $view = $this->view(array('data'=>$article));
        $view->getContext()->setGroups(array(
            'article_list',
            'writer'=>array('user_list')
        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }
    /**
     * Creates a user
     * @Rest\Post("/articles")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request){
        $article= new Article();
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters['data']);

        if ($form->isValid()) {
            $article->setSlug($this->slugger->slug($article->getHeader()));
            $this->em->persist($article);
            $this->em->flush();
            $view = $this->view(array('data'=>$article),201);
            $view->getContext()->setGroups(array(
                'article_list',
                'writer'=>array('user_list')
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