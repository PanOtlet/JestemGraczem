<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerBuilder as serializer;
use AppBundle\Entity\Meme;

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

        return $this->render('meme/mem.html.twig', [
            'mem' => $mem
        ]);
    }

    /**
     * @Route("/meme/add", name="meme.add")
     */
    public function addAction(Request $request)
    {
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
            ->where('p.status = 0')
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
            ->where('p.status > 0')
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

        return $this->render('meme/index.html.twig', [
            'meme' => $meme,
            'page' => $page
        ]);
    }
}
