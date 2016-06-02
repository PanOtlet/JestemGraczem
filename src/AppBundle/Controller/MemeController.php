<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\Meme;

class MemeController extends Controller
{
    /**
     * @Route("/meme/img/{id}", name="meme.id")
     */
    public function memAction($id)
    {
        $mem = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['id' => $id]);

        return $this->render('meme/mem.html.twig', [
            'mem' => $mem
        ]);
    }

    /**
     * @Route("/meme/add", name="meme.add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'label' => 'Tytuł',
                'required' => true
            ])
            ->add('source', TextType::class, [
                'label' => 'Źródło',
                'required' => true
            ])
            ->add('file', FileType::class, [
                'label' => 'Plik',
                'required' => true,
                'attr' => [
                    'class' => 'btn-raised  m-add',
                    'placeholder' => 'Wybierz mem'
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

            $data = new Meme();

            $file = $form->get('file')->getData();

            $extension = $file->guessExtension();
            if (!$extension) {
                $extension = 'png';
            }

            $fileName = md5(uniqid()) . '.' . $extension;

            $file->move($this->getParameter('mem_upload'), $fileName);

            $data->setUser($this->getUser()->getId());
            $data->setTitle($form->get('title')->getViewData());
            $data->setSource($form->get('source')->getViewData());
            $data->setDate(new \DateTime("now"));
            $data->setCategory(0);
            $data->setPoints(0);
            $data->setStatus(0);
            $data->setFile($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'danger',
                'Dodano mem! Teraz Twój mem pojawił się w poczekalni i oczekuje akceptacji przez administrację, by pojawić się na głównej!'
            );
            return $this->redirectToRoute('meme');
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
        if ($page < 0 || !is_numeric($page)){
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

        return $this->render('meme/index.html.twig', [
            'meme' => $meme,
            'page' => $page
        ]);
    }

    /**
     * @Route("/meme/{page}", name="meme")
     */
    public function indexAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)){
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

        return $this->render('meme/index.html.twig', [
            'meme' => $meme,
            'page' => $page
        ]);
    }
}
