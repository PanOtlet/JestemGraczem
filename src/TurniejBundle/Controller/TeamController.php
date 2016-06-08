<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TeamController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('tournament');
    }

    /**
     * @Route("/t/{name}")
     */
    public function teamAction($name = NULL)
    {
        if ($name == NULL) {
            return $this->redirectToRoute('tournament');
        }
        return $this->render('team/team.html.twig', [

        ]);
    }

    /**
     * @Route("/add")
     */
    public function addAction()
    {
        return $this->render('team/add.html.twig', [

        ]);
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render('team/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/join")
     */
    public function joinAction()
    {
        return $this->render('team/join.html.twig', [

        ]);
    }

    /**
     * @Route("/remove")
     */
    public function removeAction()
    {
        return $this->render('team/remove.html.twig', [

        ]);
    }

}
