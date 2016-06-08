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

        return $this->render('stream/tv.html.twig', [
            'stream' => $stream
        ]);
    }
}
