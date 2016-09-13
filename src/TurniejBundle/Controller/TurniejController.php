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

        if ($turniej->getPlayerType() == 0) {
            $data = [
                'entity' => 'AppBundle:User',
                'name' => 'u.username',
                'email' => ', u.email'
            ];
        } else {
            $data = [
                'entity' => 'TurniejBundle:Division',
                'name' => 'u.name',
                'email' => ''
            ];
        }

        $query = $em->getRepository('TurniejBundle:EntryTournament')->createQueryBuilder('p')
            ->where('p.tournamentId = :id')
            ->setParameter('id', $turniej->getId())
            ->leftJoin($data['entity'], "u", "WITH", "u.id=p.playerId")
            ->select($data['name'] . ', p.status' . $data['email'])
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
                'Nie jesteś organizatorem turnieju!'
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
            $turniej->setEnd(0);

            $em->flush();

            return $this->redirectToRoute('tournament.id', ['id' => $turniej->getId()]);
        }

        return $this->render('tournament/edit.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/turniej/{id}/invite", name="tournament.id.invite")
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction($id = NULL, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        if ($id == NULL || $turniej == NULL) {
            return $this->createNotFoundException('Brak takiego turnieju!');
        }

        if ($this->getUser()->getId() != $turniej->getOwner()) {
            $this->addFlash(
                'danger',
                'Nie jesteś organizatorem turnieju!'
            );
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        $form = $this->createFormBuilder()
            ->add('name', NULL, [
                'label' => 'tournament.invite-name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'tournament.invite-desc'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            switch ($turniej->getPlayerType()) {
                case 0:
                    $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $form->get('name')->getViewData()]);

                    if ($user == NULL) {
                        $this->addFlash(
                            'error',
                            'Wiesz, że taki użytkownik nie istnieje?'
                        );
                        return $this->redirectToRoute('tournament.id.invite', ['id' => $id]);
                    }

                    $entry = new EntryTournament();
                    $entry->setPlayerId($user->getId());
                    $entry->setTournamentId($id);
                    $entry->setStatus(0);

                    $em->persist($entry);
                    $em->flush();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Zaproszenie do udziału w turnieju ' . $turniej->getName() . '!')
                        ->setFrom('invite@jestemgraczem.pl')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                ':email:tournament_invite.email.twig',
                                [
                                    'name' => $user->getUsername(),
                                    'turniej' => $turniej,
                                    'desc' => $form->get('description')->getViewData(),
                                ]
                            ),
                            'text/html'
                        );

                    $this->get('mailer')->send($message);

                    $this->addFlash(
                        'success',
                        'Użytkownik został zaproszony do udziału w turnieju!'
                    );
                    break;
                case 1:
                    $team = $em->getRepository('TurniejBundle:Division')->findOneBy(['tag' => $form->get('name')->getViewData()]);

                    if ($team == NULL) {
                        $this->addFlash(
                            'error',
                            'Wiesz, że taka drużyna nie istnieje?'
                        );
                        return $this->redirectToRoute('tournament.id.invite', ['id' => $id]);
                    }

                    $entry = new EntryTournament();
                    $entry->setPlayerId($team->getId());
                    $entry->setTournamentId($id);
                    $entry->setStatus(0);

                    $em->persist($entry);
                    $em->flush();

                    $owner = $em->getRepository('TurniejBundle:Team')->findOneBy(['id' => $team->getTeam()]);
                    $user = $em->getRepository('AppBundle:User')->findOneBy(['id' => $owner->getOwner()]);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Zaproszenie do udziału w turnieju ' . $turniej->getName() . '!')
                        ->setFrom('invite@jestemgraczem.pl')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                ':email:tournament_invite_team.email.twig',
                                [
                                    'name' => $team->getName(),
                                    'team' => $owner->getName(),
                                    'id' => $team->getId(),
                                    'turniej' => $turniej,
                                    'desc' => $form->get('description')->getViewData(),
                                ]
                            ),
                            'text/html'
                        );

                    $this->get('mailer')->send($message);

                    $this->addFlash(
                        'success',
                        'Drużyna została zaproszona do udziału w turnieju!'
                    );
                    break;
                default:
                    $this->addFlash(
                        'error',
                        'Coco Jumbo i do przodu, dobre nie?!'
                    );
            }

            return $this->redirectToRoute('tournament.id', ['id' => $turniej->getId()]);
        }

        return $this->render('tournament/edit.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/turniej/{id}/invite/accept/{team}", name="tournament.id.invite.accept")
     * @param null $id
     * @param null $team
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inviteAcceptAction($id = NULL, $team = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        if ($id == NULL || $turniej == NULL) {
            return $this->createNotFoundException('Brak takiego turnieju!');
        }

        switch ($turniej->getPlayerType()) {
            case 0:
                $entry = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
                    'playerId' => $this->getUser()->getId(),
                    'tournamentId' => $id
                ]);

                if ($entry == NULL) {
                    $this->addFlash(
                        'error',
                        'Ale nie masz zaproszenia!'
                    );
                    return $this->redirectToRoute('tournament.id', ['id' => $id]);
                }
                break;
            case 1:
                $entry = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
                    'playerId' => $team,
                    'tournamentId' => $id
                ]);

                if ($entry == NULL) {
                    $this->addFlash(
                        'error',
                        'Ale nie masz zaproszenia!'
                    );
                    return $this->redirectToRoute('tournament.id', ['id' => $id]);
                }
                break;
            default:
                $this->addFlash(
                    'error',
                    'Coś się popsuło i nie było mnie słychać!'
                );
                return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        $entry->setStatus(2);

        $em->merge($entry);
        $em->flush();

        $this->addFlash(
            'success',
            'Przyjęto zaproszenie!'
        );
        return $this->redirectToRoute('tournament.id', ['id' => $turniej->getId()]);
    }

    /**
     * @Route("/dolacz/{id}/indywidualnie", name="tournament.join")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function joinAction($id)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dołącz do turnieju :: JestemGraczem.pl');

        $em = $this->getDoctrine()->getManager();
        $exist = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
            'tournamentId' => $id,
            'playerId' => $this->getUser()->getId()
        ]);

        if ($exist != NULL) {
            $this->addFlash(
                'error',
                'Już jesteś zapisany!'
            );
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

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
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
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
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        //Zapisanie uczestnika
        $zapis = new EntryTournament();
        $zapis->setPlayerId($this->getUser()->getId());
        $zapis->setTournamentId($id);
        if ($turniej->getCost() == 1) {
            $zapis->setStatus(1);
        } else {
            $zapis->setStatus(2);
        }
        $em->persist($zapis);
        $em->flush();

        $this->addFlash(
            'success',
            'Gratulacje! Dołączyłeś do turnieju! Oczekuj teraz informacji od organizatorów!'
        );
        return $this->redirectToRoute('tournament.id', ['id' => $id]);
    }

    /**
     * @Route("/dolacz/{id}/druzynowo", name="tournament.joins")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function joinsAction(Request $request, $id)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dołącz do turnieju :: JestemGraczem.pl');

        $em = $this->getDoctrine()->getManager();
        $query = $this->getDoctrine()->getRepository('TurniejBundle:TeamM8')->createQueryBuilder('p')
            ->where('p.playerId = :playerId')
            ->setParameter('playerId', $this->getUser()->getId())
            ->orderBy('p.id', 'ASC')
            ->leftJoin("TurniejBundle:Division", "t", "WITH", "t.id=p.divisionId")
            ->select('t.id, t.name, t.tag')
            ->getQuery();

        $teams = $query->getResult();

        foreach ($teams as $team) {
            $teamz[$team['name']] = $team['id'];
        }

        $form = $this->createFormBuilder()
            ->add('team', ChoiceType::class, [
                'label' => 'team.name',
                'placeholder' => 'team.name',
                'required' => true,
                'choices' => $teamz
            ])
            ->add('save', SubmitType::class, ['label' => 'tournament.join'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
                return $this->redirectToRoute('tournament.id', ['id' => $id]);
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
                return $this->redirectToRoute('tournament.id', ['id' => $id]);
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
                return $this->redirectToRoute('tournament.id', ['id' => $id]);
            }

            /*
             * @TODO: Dodać sprawdzenie, czy jest turniej na wpisowe!
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
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        return $this->render('team/join.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }
}
