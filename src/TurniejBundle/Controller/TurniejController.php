<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TurniejController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/turniej/{id}", name="tournament.id")
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

        $entry = $em->getRepository('TurniejBundle:EntryTournament')->findBy(['id' => $id]);

        return $this->render('tournament/turniej.html.twig', [
            'color' => $this->color,
            'turniej' => $turniej,
            'entry' => $entry,
        ]);
    }

}
