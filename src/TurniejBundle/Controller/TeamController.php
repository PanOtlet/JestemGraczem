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
     * @Route("/druzyna/{tag}", name="team")
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
            'divisions' => $division,
            'owner' => $owner
        ]);
    }

    /**
     * @Route("/druzyna/{tag}/remove", name="team.remove")
     */
    public function removeAction($tag = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('TurniejBundle:Team')->findOneBy(['tag' => $tag]);

        if ($tag == NULL || $team == NULL) {
            return $this->redirectToRoute('teams');
        }

        if ($this->getUser()->getId() != $team->getOwner()) {
            $this->addFlash(
                'danger',
                'Nie jesteś właścicielem drużyny!'
            );
            return $this->redirectToRoute('teams');
        }

        $em->remove($team);
        $em->flush();

        $this->addFlash(
            'danger',
            'Drużyna została usunięta!'
        );
        return $this->redirectToRoute('teams');
    }

    /**
     * @Route("/dywizja/{id}", name="division")
     */
    public function divisionAction($id = NULL)
    {
        $team = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findOneBy(['name' => $id]);

        if ($id == NULL || $team == NULL) {
            return $this->redirectToRoute('tournament');
        }

        return $this->render('team/division.html.twig', [
            'team' => $team
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
    public function joinAction(Request $request)
    {

        $team = new Division();

        $form = $this->createFormBuilder($team)
            ->add('name', NULL, [
                'label' => 'Nazwa',
                'attr' => [
                    'placeholder' => 'Podaj pełną nazwę dywizji!'
                ]
            ])
            ->add('pass', NULL, [
                'label' => 'Hasło dostępowe'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Dodaj',
                'attr' => [
                    'class' => 'btn-raised btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findOneBy([
                'name' => $form->get('name')->getViewData(),
                'pass' => $form->get('pass')->getViewData()
            ]);

            if ($division == NULL) {
                $this->addFlash(
                    'danger',
                    'Źle wprowadzona nazwa lub hasło! Upewnij się, że podajesz poprawne dane!'
                );
                return $this->redirectToRoute('teams');
            }

            $members = $division->getMembers();

            foreach ($members as $member) {
                if ($member['user'] == $this->getUser()->getUsername()) {
                    $this->addFlash(
                        'danger',
                        'Już jesteś w drużynie!'
                    );
                    return $this->redirectToRoute('division', ['id' => $division->getName()]);
                }
            }

            $members[] = [
                'user' => $this->getUser()->getUsername(),
                'role' => 'Nowy'
            ];

            $division->setMembers($members);

            $em->persist($division);
            $em->flush();

            $this->addFlash(
                'danger',
                'Gratulacje! Dostałeś się do drużyny!'
            );
            return $this->redirectToRoute('division', ['id' => $division->getName()]);
        }

        return $this->render('team/join.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
