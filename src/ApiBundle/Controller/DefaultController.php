<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('ApiBundle::index.html.twig');
    }

    public static function badRequest($json = "[]"){
        return new Response(
            $json,
            Response::HTTP_BAD_REQUEST,
            ['content-type' => 'application/json']
        );
    }
}