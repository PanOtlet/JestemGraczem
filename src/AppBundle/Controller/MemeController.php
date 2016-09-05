<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Meme;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MemeController extends Controller
{

    protected $color = "green";

    /**
     * @Route("/img/{id}", name="meme.id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function memAction($id)
    {
        $mem = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['id' => $id]);

        if ($mem == NULL) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($mem->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze memy tylko u nas! ' . $mem->getTitle())
            ->addMeta('property', 'og:title', $mem->getTitle())
            ->addMeta('property', 'og:description', 'Najlepsze memy tylko u nas! ' . $mem->getTitle())
            ->addMeta('property', 'og:image', $this->get('router')->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL) . '/assets/mem/' . $mem->getFile())
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/mem.html.twig', [
            'color' => $this->color,
            'mem' => $mem
        ]);
    }

    /**
     * @Route("/add", name="meme.add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj mem :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj swój własny mem na stronę!')
            ->addMeta('property', 'og:title', 'Dodaj mem')
            ->addMeta('property', 'og:description', 'Dodaj swój własny mem na stronę!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $mem = new Meme();

        $form = $this->createFormBuilder($mem)
            ->add('title', NULL, [
                'label' => 'mem.title'
            ])
            ->add('source', NULL, [
                'label' => 'mem.source'
            ])
            ->add('file', NULL, [
                'label' => 'mem.file',
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'mem.add',
                'attr' => [
                    'class' => 'btn-raised btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $mem->upload();
            $mem->setUser($this->getUser()->getId());
            $mem->setTitle($form->get('title')->getViewData());
            $mem->setSource($form->get('source')->getViewData());
            $mem->setDate(new \DateTime("now"));
            $mem->setCategory(0);
            $mem->setPoints(0);
            $mem->setPromoted(0);
            $mem->setAccept(false);
            $em->persist($mem);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano mem! Teraz Twój mem pojawił się w poczekalni i oczekuje akceptacji przez administrację, by pojawić się na głównej!'
            );
            return $this->redirectToRoute('meme.all');
        }

        return $this->render('meme/add.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wszystkie/{page}", name="meme.all")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function allAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('meme.all');
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Meme');
        $query = $em->createQueryBuilder('p')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.user")
            ->select('p.id, p.title, p.source, p.url, p.date, p.category')
            ->addSelect('u.username')
            ->getQuery()
            ->setMaxResults(10);

        $meme = $query->getResult();

        if ($meme == NULL) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Poczekalnia dla memów. Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'To miejsce na wszystkie memy, które jeszcze nie przeszły walidacji lub pozostaną w czyściu! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Poczekalnia dla memów. Strona ' . $page)
            ->addMeta('property', 'og:description', 'To miejsce na wszystkie memy, które jeszcze nie przeszły walidacji lub pozostaną w czyściu! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme.all', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/wait.html.twig', [
            'color' => $this->color,
            'meme' => $meme,
            'page' => $page
        ]);
    }

    /**
     * @Route("/{page}", name="meme")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('meme');
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Meme');
        $query = $em->createQueryBuilder('p')
            ->where('p.accept = true')
            ->setMaxResults(10)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.user")
            ->select('p.id, p.title, p.source, p.url, p.date, p.category')
            ->addSelect('u.username')
            ->getQuery();

        $meme = $query->getResult();

        if ($meme == NULL) {
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $promoted = $em->createQueryBuilder('e')
            ->where('e.promoted = true')
            ->orderBy('e.id','DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najlepsze memy! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze gamingowe memy w całym internecie! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Najlepsze memy! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Najlepsze gamingowe memy w całym internecie! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/index.html.twig', [
            'color' => $this->color,
            'meme' => $meme,
            'page' => $page,
            'promoted' => $promoted
        ]);
    }
}
