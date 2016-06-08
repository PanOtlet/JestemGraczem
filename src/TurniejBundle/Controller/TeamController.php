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
        $teams = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findAll();

        return $this->render('team/team.html.twig', [
            'teams' => $teams
        ]);
    }

    /**
     * @Route("/t/{name}")
     */
    public function teamAction($name = NULL)
    {
        $team = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $name]);

        if ($name == NULL || $team == NULL) {
            return $this->redirectToRoute('tournament');
        }

        $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findAll(['team' => $team->getId()]);

        return $this->render('team/team.html.twig', [
            'team' => $team,
            'division' => $division
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
