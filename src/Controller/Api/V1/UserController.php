<?php


namespace App\Controller\Api\V1;


use App\Entity\User;
use App\Form\RegisterFormType;
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
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
/**
 * Class User
 *
 * @OA\Tag(name="User")
 */
class UserController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(EntityManagerInterface $em,UserPasswordHasherInterface $passwordHasher){
        $this->em=$em;
        $this->passwordHasher=$passwordHasher;
    }

    /**
     * @Rest\Get("/users/{id}")
     * @ParamConverter("user", class = "App\Entity\User")
     * @param User $user
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Returns a User",
     *     @OA\JsonContent(
     *              type="object",
     *              title="A User",
     *               @OA\Property(property="data", type="object",ref=@Model(type=User::class,groups={"user_list"})),
     *    )
     * )
     */
    public function show(User $user){
        $view = $this->view(array('data'=>$user));
        $view->getContext()->setGroups(array(
            'user_list',
        ));
        $view->getContext()->setSerializeNull(true);
        return $this->handleView($view);
    }


    /**
     * Creates a user
     * @Rest\Post("/users")
     * @param Request $request
     * @return Response
     *     @OA\RequestBody(
     *         description="add new User",
     *         required=true,
     *       @OA\MediaType(
     *           mediaType="raw",
     *           @OA\Schema(type="object",
     *             @OA\Property(property="data", ref=@Model(type=User::class,groups={"create_user"}))
     *         )
     *      )
     *    )
     *    @OA\Response(
     *           response="201",
     *           description="Response",
     *        @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref=@Model(type=User::class)),
     *        )
     *    )
     */
    public function create(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters['data']);

        if ($form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);
            $user->setEmail($form->get('email')->getData());
            $user->setName($form->get('name')->getData());
            $user->setSurname($form->get('surname')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $view = $this->view(array('data'=>$user),201);
            $view->getContext()->setGroups(array(
                'user_list',
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