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
     * @Route("/video", name="video")
     */
    public function indexAction(Request $request)
    {
        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->findBy(['status' => 1]);
        $promoted = $this->getDoctrine()->getRepository('AppBundle:Video')->findBy(['status' => 2]);
        return $this->render('video/index.html.twig', [
            'video' => $video,
            'promoted' => $promoted
        ]);
    }

    /**
     * @Route("/video/add", name="video.add")
     */
    public function newsAddAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, array('label' => 'Link do filmu', 'required' => true))
            ->add('title', TextType::class, array('label' => 'Tytuł', 'required' => true))
            ->add('save', SubmitType::class, array('label' => 'Dodaj film'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $videoid = parse_url($form->get('url')->getViewData(), PHP_URL_QUERY);
            parse_str($videoid, $videoidParsed);
            $data = new Video();
            $data->setUser($this->getUser()->getId());
            $data->setTitle($form->get('title')->getViewData());
            $data->setVideoid($videoidParsed['v']);
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

}
