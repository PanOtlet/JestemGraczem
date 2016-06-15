<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stream;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;

class StreamController extends Controller
{
    /**
     * @Route("/stream", name="stream")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Stream');
        $total = $em->createQueryBuilder('e')->select('MAX(e.id)')->getQuery()->getSingleScalarResult();

        $promoted = $this->getDoctrine()->getRepository('AppBundle:Stream')->findBy(['status' => 2]);

        $seo = $this->container->get('sonata.seo.page');
        $seo->addMeta('property', 'og:title', 'Streamy na żywo')
            ->addMeta('property', 'og:type', 'website')
            ->addMeta('property', 'og:description', 'Nudzisz się? Sprawdź audycje na żywo naszych użytkowników!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('stream', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('stream/index.html.twig', [
            'promoted' => $promoted,
            'total' => $total
        ]);
    }

    /**
     * @Route("/player/{twitch}", name="stream.id")
     */
    public function streamAction($twitch = NULL)
    {
        $stream = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['twitch' => $twitch]);

        if ($stream == NULL || $twitch == NULL) {
            $this->addFlash(
                'danger',
                'Stream nie istnieje!'
            );
            return $this->redirectToRoute('stream');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->addMeta('property', 'og:title', 'Oglądaj '.$twitch.' na żywo!')
            ->addMeta('property', 'og:type', 'website')
            ->addMeta('property', 'og:description', 'Oglądnij audycję '.$twitch.' na żywo! Najciekawsze audycje na żywo tylko u nas!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('stream', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('stream/tv.html.twig', [
            'stream' => $stream
        ]);
    }
}
