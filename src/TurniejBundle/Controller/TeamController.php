<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TeamController extends Controller
{
    /**
     * @Route("/", name="teams")
     */
    public function indexAction()
    {
        $teams = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findAll();

        return $this->render('team/teams.html.twig', [
            'teams' => $teams
        ]);
    }

    /**
     * @Route("/t/{tag}", name="team")
     */
    public function teamAction($tag = NULL)
    {
        $team = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $tag]);

        if ($tag == NULL || $team == NULL) {
            return $this->redirectToRoute('tournament');
        }

        $owner = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['id' => $team->getOwner()]);
        $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findAll(['team' => $team->getId()]);

        return $this->render('team/team.html.twig', [
            'team' => $team,
            'division' => $division,
            'owner' => $owner
        ]);
    }

    /**
     * @Route("/add", name="team.add")
     */
    public function addAction()
    {
        return $this->render('team/add.html.twig', [

        ]);
    }

    /**
     * @Route("/edit/{tag}", name="team.edit")
     */
    public function editAction()
    {
        return $this->render('team/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/join", name="team.join")
     */
    public function joinAction()
    {
        return $this->render('team/join.html.twig', [

        ]);
    }

    /**
     * @Route("/remove/{tag}", name="team.remove")
     */
    public function removeAction()
    {
        return $this->render('team/remove.html.twig', [

        ]);
    }

}
