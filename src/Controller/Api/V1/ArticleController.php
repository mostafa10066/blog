<?php


namespace App\Controller\Api\V1;


use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Cmf\Api\Slugifier\CallbackSlugifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
/**
 * Class Article
 *
 * @OA\Tag(name="Article")
 */
class ArticleController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger,EntityManagerInterface $em){
        $this->em=$em;
        $this->slugger=$slugger;
    }

    /**
     * @Rest\Get("/articles")
     * @return Response
     * get articles
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of Articles",
     *     @OA\JsonContent(
     *              type="object",
     *              title="List of Articles",
     *              @OA\Property(
     *                   property="data",
     *                   type="object",
     *                   @OA\Property (
     *                        property="items",
     *                        type="array",
     *                        @OA\Items(ref=@Model(type=Article::class, groups={"article_list"}))
     *
     *                   )
     *
     *              ),
     *     )
     * )
     */

    public function index(){
        $articles=$this->em->getRepository(Article::class)->getArticles();
        $view = $this->view(array('data'=>$articles));
        $view->getContext()->setGroups(array(
             'Default',
              'items'=>array('article_list',
                   'writer'=>array('user_list'),
                    'teaser_image'=>array('media_list')
              ),

        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }


    /**
     * Perform a findOneBy() where the slug property matches {slug}.
     * @Rest\Get("/articles/{slug}")
     * @param Article $article
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Returns an Article",
     *     @OA\JsonContent(
     *              type="object",
     *              title="An Article",
     *               @OA\Property(property="data", type="object",ref=@Model(type=Article::class,groups={"article_details"})),
     *    )
     * )
     */
    public function show(Article $article){
        $view = $this->view(array('data'=>$article));
        $view->getContext()->setGroups(array(
            'article_details',
            'writer'=>array('user_list'),
            'teaser_image'=>array('media_list')
        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }
    /**
     * Creates an Article
     * @Rest\Post("/articles")
     * @param Request $request
     * @return Response
     *     @OA\RequestBody(
     *         description="add new Article",
     *         required=true,
     *       @OA\MediaType(
     *           mediaType="raw",
     *           @OA\Schema(type="object",
     *             @OA\Property(property="data", ref=@Model(type=Article::class,groups={"create_article"}))
     *         )
     *      )
     *    )
     *    @OA\Response(
     *           response="201",
     *           description="Response",
     *        @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref=@Model(type=Article::class)),
     *        )
     *    )
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
                'writer'=>array('user_list'),
                'teaser_image'=>array('media_list')
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