<?php

namespace App\Service;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class UploadFile
{
    private $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger=$slugger;
    }
    public function uploadFiles($brochureFile): array
    {
        $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.sha1(uniqid(mt_rand(), true)).'.'.$brochureFile->guessExtension();
        // Move the file to the directory where brochures are stored
        $destination='upload/'.date('Y-m');
        try {
            $brochureFile->move(
                $destination,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return ['displayName'=>$safeFilename,'saveName'=>$newFilename,'path'=>$destination];
    }
}
