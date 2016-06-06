<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stream;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\Video;

class StreamController extends Controller
{
    /**
     * @Route("/stream/add", name="stream.add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['label' => 'Link do kanału Twitch', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Dodaj'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $twitch = [
                'http://twitch.tv/',
                'https://twitch.tv/',
                'http://www.twitch.tv/',
                'https://www.twitch.tv/'
            ];
            $name = str_replace($twitch,NULL,$form->get('url')->getViewData());

            $data = new Stream();
            $data->setUser($this->getUser()->getId());
            $data->setName($name);
            $data->setStatus(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'danger',
                'Dodano kanał do poczekalni! Po akceptacji kanał powinien być dostępny dla wszystkich!'
            );
            return $this->redirectToRoute('stream');
        }

        return $this->render('stream/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/stream/poczekalnia/{page}", name="stream.wait")
     */
    public function waitAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Stream');
        $stream = $em->createQueryBuilder('p')
            ->where('p.status = 0')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        return $this->render('stream/index.html.twig', [
            'streams' => $stream,
            'page' => $page
        ]);
    }

    /**
     * @Route("/stream/{page}", name="stream")
     */
    public function indexAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Stream');
        $stream = $em->createQueryBuilder('p')
            ->where('p.status = 1')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        $promoted = $this->getDoctrine()->getRepository('AppBundle:Stream')->findBy(['status' => 2]);
        return $this->render('stream/index.html.twig', [
            'streams' => $stream,
            'promoted' => $promoted,
            'page' => $page
        ]);
    }

    /**
     * @Route("/player/{id}", name="stream.id")
     */
    public function streamAction($id)
    {
        $stream = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['id' => $id]);

        return $this->render('stream/tv.html.twig', [
            'stream' => $stream
        ]);
    }
}