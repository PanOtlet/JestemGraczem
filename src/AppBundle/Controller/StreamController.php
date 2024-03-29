<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StreamController extends Controller
{
    /**
     * @Route("/stream", name="stream", options={"sitemap" = true})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $total = $em->createQueryBuilder('e')->select('MAX(e.id)')->getQuery()->getSingleScalarResult();

        $promoted = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(['twitch' => !NULL]);

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Streamy na żywo :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Nudzisz się? Sprawdź audycje na żywo naszych użytkowników!')
            ->addMeta('property', 'og:title', 'Streamy na żywo')
            ->addMeta('property', 'og:description', 'Nudzisz się? Sprawdź audycje na żywo naszych użytkowników!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('stream', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('stream/index.html.twig', [
            'promoted' => $promoted,
            'total' => $total
        ]);
    }

    /**
     * @Route("/player/{twitch}", name="stream.id")
     * @param null $twitch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function streamAction($twitch = NULL)
    {
        $stream = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['twitch' => $twitch]);

        if ($stream == NULL || $twitch == NULL) {
            $this->addFlash(
                'error',
                'Stream nie istnieje!'
            );
            throw $this->createNotFoundException('Stream nie istnieje!');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Oglądaj '.$twitch.' na żywo! :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Oglądnij audycję '.$twitch.' na żywo! Najciekawsze audycje na żywo tylko u nas!')
            ->addMeta('property', 'og:title', 'Oglądaj '.$twitch.' na żywo!')
            ->addMeta('property', 'og:description', 'Oglądnij audycję '.$twitch.' na żywo! Najciekawsze audycje na żywo tylko u nas!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('stream', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('stream/tv.html.twig', [
            'stream' => $stream
        ]);
    }
}
