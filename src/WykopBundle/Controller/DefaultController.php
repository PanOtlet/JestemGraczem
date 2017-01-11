<?php

namespace WykopBundle\Controller;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WykopBundle\Entity\BlogComments;
use WykopBundle\Entity\BlogPosts;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mikroblog")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Mikroblog :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Mikroblog to miejsce dla każdego gracza do wyrażania siebie, swoich emocji i rozmowy na tematy dotyczące gier! JestemGraczem.pl")
            ->addMeta('property', 'og:title', 'Mikroblog :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('mikroblog', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $article = $this->getDoctrine()->getRepository('WykopBundle:BlogPosts')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            array_reverse($article),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render($this->getParameter('theme') . '/mikroblog/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/wpis/{id}", name="mikroblog.id")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function simplePostAction(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository('WykopBundle:BlogPosts')->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->where('p.id = :postId')
            ->setParameter('postId', $id)
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.author")
            ->addSelect('u.username')
            ->select('p.id, p.title, p.author, p.date, p.text, u.username, u.email')
            ->getQuery()
            ->getSingleResult();

        $comments = $this->getDoctrine()->getRepository('WykopBundle:BlogComments')->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.author")
            ->addSelect('u.username')
            ->where('p.postId = :postId')
            ->setParameter('postId', $id)
            ->select('p.id, p.author, p.text, u.username, u.email')
            ->getQuery()->getResult();
//        $comments = $this->getDoctrine()->getRepository('WykopBundle:BlogComments')->findBy(['postId' => $id]);

        if ($post == NULL) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($post['title'] . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze memy tylko u nas! ' . $post['title'])
            ->addMeta('property', 'og:title', $post['title'])
            ->addMeta('property', 'og:description', 'Najlepsze memy tylko u nas! ' . $post['title'])
            ->addMeta('property', 'og:url', $this->get('router')->generate('mikroblog.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        $commentsPost = new BlogComments();

        $form = $this->createFormBuilder($commentsPost)
            ->add('text', TextareaType::class, [
                'label' => 'mikroblog.text',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'mikroblog.add',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $commentsPost->setAuthor($this->getUser()->getId());
            $commentsPost->setText($form->get('text')->getViewData());
            $commentsPost->setPostId($id);
            $em->persist($commentsPost);
            $em->flush();

            return $this->redirectToRoute('mikroblog.id', ['id' => $id]);
        }

        return $this->render($this->getParameter('theme') . '/mikroblog/news.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add", name="mikroblog.add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj wpis :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('mikroblog.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $post = new BlogPosts();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, [
                'label' => 'mikroblog.title'
            ])
            ->add('text', CKEditorType::class, [
                'base_path' => 'ckeditor',
                'js_path' => 'ckeditor/ckeditor.js',
                'label' => 'mikroblog.text'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'mikroblog.add',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $post->setAuthor($this->getUser()->getId());
            $post->setTitle($form->get('title')->getViewData());
            $post->setText($form->get('text')->getViewData());
            $post->setDate(new \DateTime("now"));
            $em->persist($post);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano wpis na bloga'
            );
            return $this->redirectToRoute('mikroblog');
        }

        return $this->render($this->getParameter('theme') . '/mikroblog/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
