<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $turnieje = $em->getRepository('TurniejBundle:Turnieje')->findBy(['promoted' => 1]);

        return $this->render('tournament/index.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/all/{page}", name="tournament.all")
     */
    public function allAction($page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStart < :date')
            ->andWhere('p.dataStop > :date')
            ->setParameter('date', $date)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10);

        $turnieje = $query->getResult();

        return $this->render('tournament/all.html.twig', [
            'turnieje' => $turnieje,
            'color' => $this->color,
        ]);
    }
}
