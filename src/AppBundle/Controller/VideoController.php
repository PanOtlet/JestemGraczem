<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\Video;

class VideoController extends Controller
{
    /**
     * @Route("/video/add", name="video.add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['label' => 'Link do filmu', 'required' => true])
            ->add('title', TextType::class, ['label' => 'Tytuł', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Dodaj film'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $videoId = parse_url($form->get('url')->getViewData(), PHP_URL_QUERY);
            parse_str($videoId, $videoIdParsed);

            $videoUrl = 'https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $videoIdParsed['v'];

            $response = substr(get_headers($videoUrl)[0], 9, 3);

            if ($response != "200") {
                $this->addFlash(
                    'danger',
                    'Błąd! Film nie istnieje lub nie pochodzi z serwisu YouTube!'
                );
                return $this->redirectToRoute('video');
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
            return $this->redirectToRoute('video');
        }

        return $this->render('video/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/video/poczekalnia/{page}", name="video.wait")
     */
    public function waitAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
        $video = $em->createQueryBuilder('p')
            ->where('p.status = 0')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        if ($video == NULL) {
            $this->addFlash(
                'danger',
                'Więcej filmów nie mamy :('
            );
            return $this->redirectToRoute('video.wait');
        }

        return $this->render('video/index.html.twig', [
            'videos' => $video,
            'page' => $page
        ]);
    }

    /**
     * @Route("/video/{page}", name="video")
     */
    public function indexAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
        $video = $em->createQueryBuilder('p')
            ->where('p.status > 1')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        if ($video == NULL) {
            $this->addFlash(
                'danger',
                'Więcej filmów nie mamy :('
            );
            return $this->redirectToRoute('video');
        }

        $promoted = $this->getDoctrine()->getRepository('AppBundle:Video')->findBy(['status' => 2]);

        return $this->render('video/index.html.twig', [
            'videos' => $video,
            'promoted' => $promoted,
            'page' => $page
        ]);
    }

    /**
     * @Route("/tv/{id}", name="video.id")
     */
    public function tvAction($id)
    {
        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['id' => $id]);

        if ($video == NULL) {
            $this->addFlash(
                'danger',
                'Nie mamy tego filmu :('
            );
            return $this->redirectToRoute('video');
        }

        return $this->render('video/tv.html.twig', [
            'video' => $video
        ]);
    }
}
