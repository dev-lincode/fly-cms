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

        $uploadDir = substr($this->get('kernel')->getRootDir(), 0, -3) . '/web/' . $destiny;
        if(!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadfile = $_FILES['file']['name'];
        $extension = explode('.', $uploadfile);
        $extension = $extension[count($extension) - 1];

        $fileName = md5($uploadfile . date('dmYHis')) . '.' . $extension;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);

        return new Response($destiny . $fileName);
    }
}
