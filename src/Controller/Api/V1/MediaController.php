<?php


namespace App\Controller\Api\V1;


use App\Entity\Media;
use App\Form\UploadFileType;
use App\Service\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends AbstractFOSRestController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }

    /**
     * @Route("/media", name="create_media",methods={"POST"})
     * @param UploadFile $uploadFile
     * @param Request $request
     * @return Response
     */

    public function createMedia(UploadFile $uploadFile,Request $request):Response
    {
        $form=$this->createForm(UploadFileType::class);
        $form->handleRequest($request);
        $submitData = array(
            "image" => $request->files->get('image'),
        );
        $form->submit($submitData);
        if ($form->isValid()) {
            $response=[];
            foreach ($request->files->get('image') as $eachImage){
                $uploadedFile=$uploadFile->uploadFiles($eachImage);
                $media = new Media();
                $media->setPath($uploadedFile['path']);
                $media->setName($uploadedFile['saveName']);
                $this->em->persist($media);
                $this->em->flush();
                array_push($response,$media);
            }
            $view = $this->view(array('data'=>$response),201);
            $view->getContext()->setGroups(array(
                'media_list',
            ));
            return $this->handleView($view);

        }
        else{
            $errors=$this->view($form, Response::HTTP_BAD_REQUEST);

            return $this->handleView($errors);

        }
    }
}