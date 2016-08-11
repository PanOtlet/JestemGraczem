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
     * @Route("/aktualne/{page}", name="tournament.aktualne")
     */
    public function aktualneAction($page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje w trakcie rozgrywek :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Aktualnie odbywające się turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje w trakcie rozgrywek :: JestemGraczem.pl')
            ->addMeta('property', 'og:description', 'Aktualnie odbywające się turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.aktualne', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

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

    /**
     * @Route("/zapowiedziane/{page}", name="tournament.zapowiedziane")
     */
    public function zapowiedzianeAction($page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Zapowiedziane turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.zapowiedziane', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStart > :date')
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

    /**
     * @Route("/zakonczone/{page}", name="tournament.zakonczone")
     */
    public function zakonczoneAction($page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Zapowiedziane turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.zapowiedziane', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        $date = new \DateTime("now");

        $em = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje');
        $query = $em->createQueryBuilder('p')
            ->where('p.dataStop < :date')
            ->andWhere('p.dataStart < :date')
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

    /**
     * @Route("/turniej/{id}", name="tournament.id")
     */
    public function turniejAction($id)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Zapowiedziane turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Przyszłe turnieje na platformie JestemGraczem.pl!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        $turniej = $this->getDoctrine()->getRepository('TurniejBundle:Turnieje')->findOneBy(['id'=>$id]);

        return $this->render('tournament/turniej.html.twig', [
            'turniej' => $turniej,
            'color' => $this->color,
        ]);
    }

    /**
     * @Route("/utworz", name="tournament.create")
     */
    public function createAction()
    {
        $seo = $this->container->get('sonata.seo.page')->setTitle('Utwórz turniej :: JestemGraczem.pl');

        return $this->render('tournament/create.html.twig', [
            'color' => $this->color,
        ]);
    }

}
