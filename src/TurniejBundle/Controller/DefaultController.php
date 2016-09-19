<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TurniejBundle\Entity\Division;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use TurniejBundle\Entity\EntryTournament;
use TurniejBundle\Entity\Turnieje;
use TurniejBundle\Repository\TurniejeRepository;

class DefaultController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/", name="tournament")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $em = $this->getDoctrine()->getManager();
        $promoted = $em->getRepository('TurniejBundle:Turnieje')->findBy(['promoted' => 1, 'end' => FALSE]);
        $em = $this->getDoctrine()->getRepository('TurniejBundle:EntryTournament');
        $query = $em->createQueryBuilder('p')
            ->where('p.playerId = :playerId')
            ->setParameter('playerId', $this->getUser()->getId())
            ->orderBy('p.id', 'DESC')
            ->leftJoin("TurniejBundle:Turnieje", "t", "WITH", "t.id=p.tournamentId")
            ->select('t.id, t.name, p.status')
            ->getQuery();

        $my = $query->getResult();

        return $this->render('tournament/index.html.twig', [
            'color' => $this->color,
            'promo' => $promoted,
            'my' => $my
        ]);
    }

    /**
     * @Route("/otwarte/{page}", name="tournament.open")
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function openAction(Request $request, $page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje w trakcie zapisów :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Turnieje w trakcie zapisów na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje w trakcie zapisów :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Turnieje w trakcie zapisów na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.open', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStart < :date')
            ->andWhere('p.dataStop > :date')
            ->andWhere('p.end = FALSE')
            ->setParameter('date', $date)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(10);

        $sort = $request->query->get('sort');
        if ($sort && $sort >= 0 && $sort <= 7) {
            $query->andWhere('p.dyscyplina = :game')->setParameter('game', $request->query->get('sort'));
        }

        $turnieje = $query->getQuery()->getResult();

        return $this->render('tournament/all.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/zapowiedziane/{page}", name="tournament.incoming")
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function incomingAction(Request $request, $page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Zapowiedziane turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Zapowiedziane turnieje :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.incoming', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStart > :date')
            ->andWhere('p.end = FALSE')
            ->setParameter('date', $date)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(10);

        $sort = $request->query->get('sort');
        if ($sort && $sort >= 0 && $sort <= 7) {
            $query->andWhere('p.dyscyplina = :game')->setParameter('game', $request->query->get('sort'));
        }

        $turnieje = $query->getQuery()->getResult();

        return $this->render('tournament/all.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/zamkniete/{page}", name="tournament.close")
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeAction(Request $request, $page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Trwające turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Trwające turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Trwające turnieje :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Trwające turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.close', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStop < :date')
            ->andWhere('p.dataStart < :date')
            ->andWhere('p.end = FALSE')
            ->setParameter('date', $date)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(10);

        $sort = $request->query->get('sort');
        if ($sort && $sort >= 0 && $sort <= 7) {
            $query->andWhere('p.dyscyplina = :game')->setParameter('game', $request->query->get('sort'));
        }

        $turnieje = $query->getQuery()->getResult();

        return $this->render('tournament/all.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/zakonczone/{page}", name="tournament.end")
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function endAction(Request $request, $page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje zakończone :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Turnieje zakończone na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje zakończone :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Turnieje zakończone na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.end', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));


        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.end = TRUE')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(10);

        $sort = $request->query->get('sort');
        if ($sort && $sort >= 0 && $sort <= 7) {
            $query->andWhere('p.dyscyplina = :game')->setParameter('game', $request->query->get('sort'));
        }

        $turnieje = $query->getQuery()->getResult();

        return $this->render('tournament/all.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/utworz", name="tournament.create")
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $this->container->get('sonata.seo.page')->setTitle('Utwórz turniej :: JestemGraczem.pl');

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, ['label' => 'tournament.name', 'required' => true])
            ->add('description', TextareaType::class, ['label' => 'tournament.description', 'required' => true])
            ->add('dateStart', DateType::class, [
                'years' => range(date('Y'), date('Y') + 4),
                'label' => 'tournament.dateStart',
                'required' => true
            ])
            ->add('dateStop', DateType::class, [
                'years' => range(date('Y'), date('Y') + 4),
                'label' => 'tournament.dateStop',
                'required' => true
            ])
            ->add('game', ChoiceType::class, [
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
            ->add('type', ChoiceType::class, [
                'label' => 'tournament.type',
                'required' => true,
                'choices' => [
                    'tournament.open' => 0,
                    'tournament.invite' => 1,
                ],
            ])
            ->add('cost', ChoiceType::class, [
                'label' => 'tournament.cost',
                'required' => true,
                'choices' => [
                    'tournament.free' => 0,
                    'tournament.fee' => 1,
                ],
            ])
            ->add('costPerTeam', NumberType::class, [
                'label' => 'tournament.costPerTeam',
                'required' => true,
                'data' => 1.0,
            ])
            ->add('costOrg', PercentType::class, [
                'label' => 'tournament.costOrg',
                'required' => true,
                'data' => 0.1,
            ])
            ->add('countTeam', ChoiceType::class, [
                'label' => 'tournament.countTeam',
                'required' => true,
                'choices' => [
                    'tournament.members.4' => 4,
                    'tournament.members.8' => 8,
                    'tournament.members.16' => 16,
                    'tournament.members.24' => 24,
                    'tournament.members.32' => 32,
                    'tournament.members.48' => 48
                ]
            ])
            ->add('playerType', ChoiceType::class, [
                'label' => 'tournament.playerType',
                'required' => true,
                'choices' => [
                    'tournament.individual' => 0,
                    'tournament.team' => 1
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'tournament.create'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = new Turnieje();
            $datas = [
                'name' => $form->get('name')->getViewData(),
                'description' => $form->get('description')->getViewData(),
                'owner' => $this->getUser()->getId(),
                'dyscyplina' => $form->get('game')->getViewData(),
                'type' => $form->get('type')->getViewData(),
                'cost' => $form->get('cost')->getViewData(),
                'costPerTeam' => $form->get('costPerTeam')->getViewData(),
                'costOrg' => $form->get('costOrg')->getViewData(),
                'countTeam' => $form->get('countTeam')->getViewData(),
                'dataStart' => $form->get('dateStart')->getData(),
                'dataStop' => $form->get('dateStop')->getData(),
                'playerType' => $form->get('playerType')->getViewData(),
            ];

            $data->createTournament($datas);

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano turniej!'
            );
            return $this->redirectToRoute('tournament');
        }

        return $this->render('tournament/create.html.twig', [
            'color' => $this->color,
            'form' => $form->createView()
        ]);
    }
}
