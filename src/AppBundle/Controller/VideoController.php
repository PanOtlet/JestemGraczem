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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoController extends Controller
{

    protected $color = "green";

    /**
     * @Route("/video/add", name="video.add")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['label' => 'video.url', 'required' => true])
            ->add('title', TextType::class, ['label' => 'video.title', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'video.add'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $videoId = parse_url($form->get('url')->getViewData(), PHP_URL_QUERY);
            parse_str($videoId, $videoIdParsed);

            $videoUrl = 'https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $videoIdParsed['v'];

            $response = substr(get_headers($videoUrl)[0], 9, 3);

            if ($response != "200") {
                $this->addFlash(
                    'error',
                    'Błąd! Film nie istnieje lub nie pochodzi z serwisu YouTube!'
                );
                return $this->redirectToRoute('video');
            }

            $data = new Video();
            $data->setUser($this->getUser()->getId());
            $data->setTitle($form->get('title')->getViewData());
            $data->setVideoid($videoIdParsed['v']);
            if ($this->getUser()->getPartner() == 1) {
                $data->setPromoted(true);
                $data->setAccept(true);
            } else {
                $data->setPromoted(false);
                $data->setAccept(false);
            }
            $data->setDateAdd(new \DateTime("now"));

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano film do poczekalni! Po akceptacji film powinien być dostępny dla wszystkich!'
            );
            return $this->redirectToRoute('video');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj film :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj swój własny film do naszej bazy!')
            ->addMeta('property', 'og:title', 'Dodaj film')
            ->addMeta('property', 'og:description', 'Dodaj swój własny film do naszej bazy!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('video.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('video/add.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/video/poczekalnia/{page}", name="video.wait")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function waitAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
        $video = $em->createQueryBuilder('p')
            ->where('p.accept = 0')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        if ($video == NULL) {
            $this->addFlash(
                'error',
                'Więcej filmów nie mamy :('
            );
            throw $this->createNotFoundException('Więcej filmów nie mamy :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Poczekalnia z filmami! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Poczekalnia z najciekawszymi filmami dostępnymi w internecie! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Poczekalnia z filmami! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Poczekalnia z najciekawszymi filmami dostępnymi w internecie! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('video.wait', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('video/index.html.twig', [
            'color' => $this->color,
            'videos' => $video,
            'page' => $page
        ]);
    }

    /**
     * @Route("/video/{page}", name="video")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 0)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Video');
        $video = $em->createQueryBuilder('p')
            ->where('p.accept = 1')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();

        if ($video == NULL) {
            $this->addFlash(
                'error',
                'Więcej filmów nie mamy :('
            );
            throw $this->createNotFoundException('Więcej filmów nie mamy :(');
        }

        $promoted = $this->getDoctrine()->getRepository('AppBundle:Video')->findBy(['promoted' => true]);

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najciekawsze filmy w internecie! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Poszukujesz ciekawych filmów w internecie? Zapraszamy do oglądania twórczości naszych użytkowników! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Najciekawsze filmy w internecie! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Poszukujesz ciekawych filmów w internecie? Zapraszamy do oglądania twórczości naszych użytkowników! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('video.wait', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('video/index.html.twig', [
            'color' => $this->color,
            'videos' => $video,
            'promoted' => $promoted,
            'page' => $page
        ]);
    }

    /**
     * @Route("/tv/{id}", name="video.id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tvAction($id)
    {
        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['id' => $id]);

        if ($video == NULL) {
            $this->addFlash(
                'error',
                'Nie mamy tego filmu :('
            );
            throw $this->createNotFoundException('Nie mamy tego filmu :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($video->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Film ' . $video->getTitle() . ' dostępny jest na platformie JestemGraczem.pl bez ograniczeń!')
            ->addMeta('property', 'og:title', $video->getTitle())
            ->addMeta('property', 'og:video', 'https://www.youtube.com/v/' . $video->getVideoid())
            ->addMeta('property', 'og:description', 'Film ' . $video->getTitle() . ' dostępny jest na platformie JestemGraczem.pl bez ograniczeń!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('video.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('video/tv.html.twig', [
            'color' => $this->color,
            'video' => $video
        ]);
    }
}
