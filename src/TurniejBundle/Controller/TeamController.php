<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use TurniejBundle\Entity\Division;
use TurniejBundle\Entity\Team;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TurniejBundle\Repository\TeamRepository;

class TeamController extends Controller
{
    /**
     * @Route("/add", name="team.add")
     */
    public function addAction(Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj drużynę :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj drużynę do największego spisu drużyn esportowych w Polsce!')
            ->addMeta('property', 'og:title', 'Dodaj drużynę')
            ->addMeta('property', 'og:description', 'Dodaj drużynę do największego spisu drużyn esportowych w Polsce!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('team.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $team = new Team();

        $form = $this->createFormBuilder($team)
            ->add('name', NULL, [
                'label' => 'team.name'
            ])
            ->add('tag', NULL, [
                'label' => 'team.tag'
            ])
            ->add('logo', NULL, [
                'label' => 'team.logo',
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('description', TextAreaType::class, [
                'label' => 'team.desc',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('shortdesc', TextAreaType::class, [
                'label' => 'team.shortdesc'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.add',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $team->uploadLogo();
            $team->setOwner($this->getUser()->getId());
            $team->setName($form->get('name')->getViewData());
            $team->setTag($form->get('tag')->getViewData());
            $team->setDescription($form->get('description')->getViewData());
            $team->setShortdesc($form->get('shortdesc')->getViewData());
            $em->persist($team);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano drużynę do spisu!'
            );
            return $this->redirectToRoute('team');
        }

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

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
}
