<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TurniejBundle\Entity\EntryTournament;

class TurniejController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/turniej/{id}", name="tournament.id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->find($id);

        if ($turniej == NULL) {
            throw $this->createNotFoundException('Turniej nie istnieje!');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turniej ' . $turniej->getName() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Turniej ' . $turniej->getName() . ' jest dostępny dzięki platformie JestemGraczem.pl')
            ->addMeta('property', 'og:title', 'Turniej ' . $turniej->getName() . ' :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Turniej ' . $turniej->getName() . ' jest dostępny dzięki platformie JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        if ($turniej->getPlayerType() == 0){
            $data = [
                'entity' => 'AppBundle:User',
                'name' => 'u.username'
            ];
        } else {
            $data = [
                'entity' => 'TurniejBundle:Division',
                'name' => 'u.name'
            ];
        }

        $query = $em->getRepository('TurniejBundle:EntryTournament')->createQueryBuilder('p')
            ->where('p.tournamentId = :id')
            ->setParameter('id', $turniej->getId())
            ->leftJoin($data['entity'], "u", "WITH", "u.id=p.playerId")
            ->select($data['name'].', p.status')
            ->orderBy($data['name'], 'ASC')
            ->getQuery();

        $entry = $query->getResult();

        return $this->render('tournament/turniej.html.twig', [
            'color' => $this->color,
            'turniej' => $turniej,
            'entry' => $entry,
        ]);
    }

    /**
     * @Route("/turniej/{id}/edit", name="tournament.id.edit")
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = NULL, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        $this->container->get('sonata.seo.page')->setTitle('Edycja turnieju :: JestemGraczem.pl');

        if ($id == NULL || $turniej == NULL) {
            return $this->createNotFoundException('Brak takiego turnieju!');
        }

        if ($this->getUser()->getId() != $turniej->getOwner()) {
            $this->addFlash(
                'danger',
                'Nie jesteś organizatorem drużyny!'
            );
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        $form = $this->createFormBuilder($turniej)
            ->add('name', NULL, [
                'label' => 'team.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'team.desc'
            ])
            ->add('dyscyplina', ChoiceType::class, [
                'label' => 'tournament.game',
                'placeholder' => 'tournament.game',
                'required' => true,
                'choices' => [
                    'tournament.csgo' => 1,
                    'tournament.lol' => 2,
                    'tournament.hots' => 3,
                    'tournament.sc2' => 4,
                    'tournament.hs' => 5,
                    'tournament.dota2' => 6,
                    'tournament.wot' => 7,
                    'tournament.other' => 0,
                ],
            ])
            ->add('end', CheckboxType::class, [
                'label' => 'tournament.end'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
                'attr' => [
                    'class' => 'btn-raised btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $turniej->setName($form->get('name')->getViewData());
            $turniej->setDescription($form->get('description')->getViewData());
            $turniej->setDyscyplina($form->get('dyscyplina')->getViewData());
            $turniej->setEnd($form->get('end')->getViewData());

            $em->flush();

            return $this->redirectToRoute('tournament.id', ['id' => $turniej->getId()]);
        }

        return $this->render('tournament/edit.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }
}
