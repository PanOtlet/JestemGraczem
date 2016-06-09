<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use TurniejBundle\Entity\Division;
use TurniejBundle\Entity\Team;

class TeamController extends Controller
{
    /**
     * @Route("/{tag}", name="team")
     */
    public function indexAction($tag = NULL)
    {
        $team = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $tag]);

        if ($tag == NULL || $team == NULL) {
            $teams = $this->getDoctrine()->getRepository('TurniejBundle:Team')->findAll();

            return $this->render('team/teams.html.twig', [
                'teams' => $teams
            ]);
        }

        $owner = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['id' => $team->getOwner()]);
        $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findAll(['team' => $team->getId()]);

        return $this->render('team/team.html.twig', [
            'team' => $team,
            'divisions' => $division,
            'owner' => $owner
        ]);
    }

    /**
     * @Route("/{tag}/edit", name="team.edit")
     */
    public function editAction($tag = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $tag]);

        if ($tag == NULL || $team == NULL) {
            return $this->redirectToRoute('team');
        }

        if ($this->getUser()->getId() != $team->getOwner()) {
            $this->addFlash(
                'danger',
                'Nie jesteś właścicielem drużyny!'
            );
            return $this->redirectToRoute('team');
        }

        $em->remove($team);
        $em->flush();

        $this->addFlash(
            'danger',
            'Drużyna została usunięta!'
        );

        return $this->render('team/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/{tag}/remove", name="team.remove")
     */
    public function removeAction($tag = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $tag]);

        if ($tag == NULL || $team == NULL) {
            return $this->redirectToRoute('team');
        }

        if ($this->getUser()->getId() != $team->getOwner()) {
            $this->addFlash(
                'danger',
                'Nie jesteś właścicielem drużyny!'
            );
            return $this->redirectToRoute('team');
        }

        $em->remove($team);
        $em->flush();

        $this->addFlash(
            'danger',
            'Drużyna została usunięta!'
        );
        return $this->redirectToRoute('team');
    }

    /**
     * @Route("/add", name="team.add")
     */
    public function addAction()
    {

        return $this->render('team/add.html.twig', [

        ]);
    }

}
