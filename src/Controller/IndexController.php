<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;

class IndexController extends AbstractController {

    public function __init() {


        $session = new Session();
        $session->set('name', 'Drak');

        return new Response(
            '<html><body>Lucky number: hello</body></html>'
        );
    }

}


?>