<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="tournament")
     */
    public function indexAction()
    {
        return $this->render('tournament/index.html.twig');
    }
}
