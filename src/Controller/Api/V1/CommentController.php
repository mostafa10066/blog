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
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
/**
 * Class Comment
 *
 * @OA\Tag(name="Comment")
 */
class CommentController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }




    /**
     * @Rest\Get("/articles/{slug}/comments")
     * @ParamConverter("article", class = "App\Entity\Article")
     * @param Article $article
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of Article Comments",
     *     @OA\JsonContent(
     *              type="object",
     *              title="List of Article Comments",
     *              @OA\Property(
     *                   property="data",
     *                   type="object",
     *                   @OA\Property (
     *                        property="items",
     *                        type="array",
     *                        @OA\Items(ref=@Model(type=Comment::class, groups={"comment_list"}))
     *
     *                   )
     *
     *              ),
     *     )
     * )
     */
    public function index(Article $article){
        $comments=$article->getComments();
        $view = $this->view(array('data'=>$comments));
        $view->getContext()->setGroups(array(
            'comment_list',
        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }
    /**
     * Creates a comment
     * @Rest\Post("/articles/{slug}/comments")
     * @param Request $request
     * @param Article $article
     * @return Response
     *     @OA\RequestBody(
     *         description="add new Comment",
     *         required=true,
     *       @OA\MediaType(
     *           mediaType="raw",
     *           @OA\Schema(type="object",
     *             @OA\Property(property="data", ref=@Model(type=Comment::class,groups={"create_comment"}))
     *         )
     *      )
     *    )
     *    @OA\Response(
     *           response="201",
     *           description="Response",
     *        @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref=@Model(type=Comment::class)),
     *        )
     *    )
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