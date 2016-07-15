<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use TurniejBundle\Entity\Division;
use TurniejBundle\Entity\Team;

class DivisionController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/add/{tag}", name="division.add")
     */
    public function addAction($tag = NULL, Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Tworzenie dywizji :: JestemGraczem.pl');

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

        $division = new Division();

        $form = $this->createFormBuilder($division)
            ->add('name', NULL, [
                'label' => 'team.divname'
            ])
            ->add('pass', NULL, [
                'label' => 'team.pass'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'team.divdesc'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.add',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $division->setTeam($team->getId());
            $division->setName($form->get('name')->getViewData());
            $division->setTag(md5($form->get('name')->getViewData()));
            $division->setPass($form->get('pass')->getViewData());
            $division->setMembers(json_encode(NULL));
            $division->setDescription($form->get('description')->getViewData());
            $em->persist($division);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano dywizję do spisu!'
            );
            return $this->redirectToRoute('team', [
                'tag' => $team->getTag()
            ]);
        }

        return $this->render('team/add.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/join", name="team.join")
     */
    public function joinAction(Request $request)
    {

        $team = new Division();

        $form = $this->createFormBuilder($team)
            ->add('tag', NULL, [
                'label' => 'team.tag'
            ])
            ->add('pass', NULL, [
                'label' => 'team.pass'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.add',
                'attr' => [
                    'class' => 'btn-raised btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findOneBy([
                'tag' => $form->get('tag')->getViewData(),
                'pass' => $form->get('pass')->getViewData()
            ]);

            if ($division == NULL) {
                $this->addFlash(
                    'danger',
                    'Źle wprowadzona nazwa lub hasło! Upewnij się, że podajesz poprawne dane!'
                );
                return $this->redirectToRoute('team');
            }

            $members = $division->getMembers();

            if (is_array($members)) {
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
            } else {
                $members = [
                    [
                        'user' => $this->getUser()->getUsername(),
                        'role' => 'Nowy'
                    ],
                ];
            }

            $division->setMembers($members);

            $em->flush();

            $this->addFlash(
                'danger',
                'Gratulacje! Dostałeś się do drużyny!'
            );
            return $this->redirectToRoute('division', ['id' => $division->getName()]);
        }

        return $this->render('team/join.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/join/{tag}", name="team.join.tag")
     */
    public function joinTagAction($tag = NULL, Request $request)
    {

        $team = new Division();

        $form = $this->createFormBuilder($team)
            ->add('pass', NULL, [
                'label' => 'team.pass'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.add',
                'attr' => [
                    'class' => 'btn-raised btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $division = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findOneBy([
                'tag' => $tag,
                'pass' => $form->get('pass')->getViewData()
            ]);

            if ($division == NULL) {
                $this->addFlash(
                    'danger',
                    'Źle wprowadzona nazwa lub hasło! Upewnij się, że podajesz poprawne dane!'
                );
                return $this->redirectToRoute('team.join');
            }

            $members = $division->getMembers();

            if (is_array($members)) {
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

            } else {
                $members = [
                    [
                        'user' => $this->getUser()->getUsername(),
                        'role' => 'Nowy'
                    ],
                ];
            }

            $division->setMembers($members);

            $em->flush();

            $this->addFlash(
                'danger',
                'Gratulacje! Dostałeś się do drużyny!'
            );
            return $this->redirectToRoute('team');
        }

        return $this->render('team/join.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="division")
     */
    public function divisionAction($id = NULL)
    {
        $team = $this->getDoctrine()->getRepository('TurniejBundle:Division')->findOneBy(['id' => $id]);

        if ($id == NULL || $team == NULL) {
            return $this->redirectToRoute('tournament');
        }

        return $this->render('team/division.html.twig', [
            'color' => $this->color,
            'team' => $team
        ]);
    }
}
