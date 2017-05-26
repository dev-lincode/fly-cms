<?php

namespace Lincode\Fly\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Configuration controller.
 *
 * @Route("/gallery")
 */
class GalleryController extends Controller {
    /**
     *
     * @Route("/upload/{destiny}", name="cms_gallery_upload")
     * @Method({"GET", "POST"})
     */
    public function uploadAction(Request $request, $destiny) {
        $destiny = str_replace("-","/",$destiny);
        if(substr($destiny, -1) != "/")
            $destiny .= "/";

        $file = $request->files->get('file');

        $uploadfile = $file->getClientOriginalName();
        $extension = explode('.', $uploadfile);
        $extension = $extension[count($extension) - 1];
        $fileName = md5($uploadfile . date('dmYHis')) . '.' . $extension;

        $uploadService = $this->get('fly.upload.service');
        $uploadService->moveFile($file, $destiny, $fileName);

        return new Response($destiny . $fileName);
    }
}
