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
    /**
     * @Route("/meme/img/{id}", name="meme.id")
     */
    public function memAction($id)
    {
        $mem = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['id' => $id]);

        if ($mem == NULL) {
            $this->addFlash(
                'danger',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            return $this->redirectToRoute('meme');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($mem->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze memy tylko u nas! ' . $mem->getTitle())
            ->addMeta('property', 'og:title', $mem->getTitle())
            ->addMeta('property', 'og:description', 'Najlepsze memy tylko u nas! ' . $mem->getTitle())
//            ->addMeta('property', 'og:image', $this->get('router')->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL) . '/assets/mem/' . $mem->getFile())
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/mem.html.twig', [
            'mem' => $mem
        ]);
    }

    /**
     * @Route("/meme/add", name="meme.add")
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
                'label' => 'Tytuł'
            ])
            ->add('source', NULL, [
                'label' => 'Źródło'
            ])
            ->add('file', NULL, [
                'label' => 'Plik',
                'attr' => [
                    'class' => 'm-add'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Dodaj',
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
            $mem->setStatus(0);
            $mem->setAccept(false);
            $em->persist($mem);
            $em->flush();

            $this->addFlash(
                'danger',
                'Dodano mem! Teraz Twój mem pojawił się w poczekalni i oczekuje akceptacji przez administrację, by pojawić się na głównej!'
            );
            return $this->redirectToRoute('meme.wait');
        }

        return $this->render('meme/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/meme/poczekalnia/{page}", name="meme.wait")
     */
    public function waitAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('meme.wait');
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Meme');
        $query = $em->createQueryBuilder('p')
            ->where('p.accept = false')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10);

        $meme = $query->getResult();

        if ($meme == NULL) {
            $this->addFlash(
                'danger',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            return $this->redirectToRoute('meme.wait');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Poczekalnia dla memów. Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'To miejsce na wszystkie memy, które jeszcze nie przeszły walidacji lub pozostaną w czyściu! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Poczekalnia dla memów. Strona ' . $page)
            ->addMeta('property', 'og:description', 'To miejsce na wszystkie memy, które jeszcze nie przeszły walidacji lub pozostaną w czyściu! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme.wait', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/wait.html.twig', [
            'meme' => $meme,
            'page' => $page
        ]);
    }

    /**
     * @Route("/meme/{page}", name="meme")
     */
    public function indexAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('meme');
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Meme');
        $query = $em->createQueryBuilder('p')
            ->where('p.accept = true')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10);

        $meme = $query->getResult();

        if ($meme == NULL) {
            $this->addFlash(
                'danger',
                'Doszedłeś do końca internetu. Jesteśmy dumni!'
            );
            return $this->redirectToRoute('meme');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najlepsze memy! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze gamingowe memy w całym internecie! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Najlepsze memy! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Najlepsze gamingowe memy w całym internecie! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('meme', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('meme/index.html.twig', [
            'meme' => $meme,
            'page' => $page
        ]);
    }
}
