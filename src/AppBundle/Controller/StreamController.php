<?php

namespace AppBundle\Controller;

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
            ->add('url', UrlType::class, array('label' => 'Link do kanału Twitch', 'required' => true))
            ->add('save', SubmitType::class, array('label' => 'Dodaj film'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $videoId = parse_url($form->get('url')->getViewData(), PHP_URL_QUERY);
            parse_str($videoId, $videoIdParsed);

            $videoUrl = 'https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $videoIdParsed['v'];

            $response = substr(get_headers($videoUrl)[0], 9, 3);

            if($response != "200"){
                $this->addFlash(
                    'danger',
                    'Błąd! Film nie istnieje lub nie pochodzi z serwisu YouTube!'
                );
                return $this->redirectToRoute('stream');
            }

            $data = new Video();
            $data->setUser($this->getUser()->getId());
            $data->setTitle($form->get('title')->getViewData());
            $data->setVideoid($videoIdParsed['v']);
            $data->setStatus(0);
            $data->setDateAdd(new \DateTime("now"));

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'danger',
                'Dodano film do poczekalni! Po akceptacji film powinien być dostępny dla wszystkich!'
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
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
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
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
        $stream = $em->createQueryBuilder('p')
            ->where('p.status = 1')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        $promoted = $this->getDoctrine()->getRepository('AppBundle:Video')->findBy(['status' => 2]);
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
