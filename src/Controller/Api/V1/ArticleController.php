<?php


namespace App\Controller\Api\V1;


use App\Entity\Article;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ArticleController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
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


}