<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TurniejBundle\Entity\Division;
use TurniejBundle\Entity\EntryTournament;
use TurniejBundle\Entity\Turnieje;

class DefaultController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/", name="tournament")
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
     */
    public function incommingAction(Request $request, $page = 0)
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
                ],
            ])
            ->add('countTeam', IntegerType::class, [
                'label' => 'tournament.countTeam',
                'required' => true,
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
            $data->setName($form->get('name')->getViewData());
            $data->setDescription($form->get('description')->getViewData());
            $data->setOwner($this->getUser()->getId());
            $data->setDyscyplina($form->get('game')->getViewData());
            $data->setType($form->get('type')->getViewData());
            $data->setCost($form->get('cost')->getViewData());
            $data->setCountTeam($form->get('countTeam')->getViewData());
            $data->setPrizePool(0);
            $data->setCostPerTeam(0);
            $data->setCostOrg(0);
            $data->setDataStart($form->get('dateStart')->getData());
            $data->setDataStop($form->get('dateStop')->getData());
            $data->setPlayerType($form->get('playerType')->getViewData());
            $data->setPromoted(0);
            $data->setEnd(0);

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

    /**
     * @Route("/dolacz/{id}/indywidualnie", name="tournament.join")
     */
    public function joinAction($id)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dołącz do turnieju :: JestemGraczem.pl');

        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        //Sprawdzenie, czy turniej istnieje
        if ($turniej == NULL) {
            throw $this->createNotFoundException('Turniej do którego chcesz dołączyć nie istnieje!');
        }

        //Sprawdzenie, czy drużynowe, czy indywidualne
        if ($turniej->getPlayerType() == 1) {
            return $this->redirectToRoute('tournament.joins', ['id' => $id]);
        }

        //Sprawdzenie, czy turniej jeszcze pozwala na zapis
        $date = new \DateTime('now');

        if ($turniej->getDataStop() < $date) {
            $this->addFlash(
                'error',
                'Turniej oficjalnie zamknął możliwość zapisu!'
            );
            return $this->redirectToRoute('tournament');
        }

        //Sprawdzenie, czy turniej nie jest pełen
        $zapis = $this->getDoctrine()->getRepository('TurniejBundle:EntryTournament')->findBy([
            'tournamentId' => $turniej->getId()
        ]);

        if (count($zapis) >= $turniej->getCountTeam()) {
            $this->addFlash(
                'error',
                'Niestety! Turniej już zapełniony po brzegi!'
            );
            return $this->redirectToRoute('tournament');
        }

        //Sprawdzanie, czy turniej jest Open, czy Invite
        switch ($turniej->getType()) {
            case 0:
                $zapis = TRUE;
                break;
            case 1:
                $zapis = $this->getDoctrine()->getRepository('TurniejBundle:EntryTournament')->findOneBy([
                    'tournamentId' => $turniej->getId(),
                    'playerId' => $this->getUser()->getId()
                ]);
                break;
            default:
                $zapis = NULL;
        }

        if ($zapis == NULL) {
            $this->addFlash(
                'error',
                'Nie masz dostępu do turnieju!'
            );
            return $this->redirectToRoute('tournament');
        }

        /*
         * @TODO: Dodać sprawdzenie, czy jest turniej na wpisowe!
         * @TODO: Dodać sprawdzenie, by uczestnik już jest zapisany w turnieju!
         */

        //Zapisanie uczestnika
        $zapis = new EntryTournament();
        $zapis->setPlayerId($this->getUser()->getId());
        $zapis->setTournamentId($id);
        $zapis->setStatus(2);
        $em->persist($zapis);
        $em->flush();

        $this->addFlash(
            'success',
            'Gratulacje! Dołączyłeś do turnieju! Oczekuj teraz informacji od organizatorów!'
        );
        return $this->redirectToRoute('tournament');
    }

    /**
     * @Route("/dolacz/{id}/druzynowo", name="tournament.joins")
     */
    public function joinsAction(Request $request, $id)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dołącz do turnieju :: JestemGraczem.pl');



        $form = $this->createFormBuilder()
            ->add('team', ChoiceType::class, [
                'label' => 'team.name',
                'placeholder' => 'team.name',
                'required' => true,
                'choices' => [
                    'kupa' => 1,
                    'tournament.lol' => 2,
                    'tournament.hots' => 3,
                    'tournament.sc2' => 4,
                    'tournament.hs' => 5,
                    'tournament.dota2' => 6,
                    'tournament.wot' => 7,
                    'tournament.other' => 0,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'tournament.join'])
            ->getForm();

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        //Sprawdzenie, czy turniej istnieje
        if ($turniej == NULL) {
            throw $this->createNotFoundException('Turniej do którego chcesz dołączyć nie istnieje!');
        }

        //Sprawdzenie, czy drużynowe, czy indywidualne
        if ($turniej->getPlayerType() == 0) {
            return $this->redirectToRoute('tournament.join', ['id' => $id]);
        }

        //Sprawdzenie, czy turniej jeszcze pozwala na zapis
        $date = new \DateTime('now');

        if ($turniej->getDataStop() < $date) {
            $this->addFlash(
                'error',
                'Turniej oficjalnie zamknął możliwość zapisu!'
            );
            return $this->redirectToRoute('tournament');
        }

        //Sprawdzenie, czy turniej nie jest pełen
        $zapis = $this->getDoctrine()->getRepository('TurniejBundle:EntryTournament')->findBy([
            'tournamentId' => $turniej->getId()
        ]);

        if (count($zapis) >= $turniej->getCountTeam()) {
            $this->addFlash(
                'error',
                'Niestety! Turniej już zapełniony po brzegi!'
            );
            return $this->redirectToRoute('tournament');
        }

        //Sprawdzanie, czy turniej jest Open, czy Invite
        switch ($turniej->getType()) {
            case 0:
                $zapis = TRUE;
                break;
            case 1:
                $zapis = $this->getDoctrine()->getRepository('TurniejBundle:EntryTournament')->findOneBy([
                    'tournamentId' => $turniej->getId(),
                    'playerId' => $this->getUser()->getId()
                ]);
                break;
            default:
                $zapis = NULL;
        }

        if ($zapis == NULL) {
            $this->addFlash(
                'error',
                'Nie masz dostępu do turnieju!'
            );
            return $this->redirectToRoute('tournament');
        }

        /*
         * @TODO: Dodać sprawdzenie, czy jest turniej na wpisowe!
         * @TODO: Dodać sprawdzenie, by uczestnik już jest zapisany w turnieju!
         */

        //Zapisanie uczestnika
        $zapis = new EntryTournament();
        $zapis->setPlayerId($this->getUser()->getId());
        $zapis->setTournamentId($id);
        $zapis->setStatus(2);
        $em->persist($zapis);
        $em->flush();

        $this->addFlash(
            'success',
            'Gratulacje! Dołączyłeś do turnieju! Oczekuj teraz informacji od organizatorów!'
        );
        return $this->redirectToRoute('tournament');
    }
}
