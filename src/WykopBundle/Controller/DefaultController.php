<?php

namespace WykopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mikroblog")
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
     */
    public function simplePostAction($id)
    {
        $post = $this->getDoctrine()->getRepository('WykopBundle:BlogPosts')->findOneBy(['id' => $id]);

        $em = $this->getDoctrine()->getRepository('WykopBundle:BlogComments');
        $query = $em->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.author")
            ->addSelect('u.username')
            ->select('p.id, p.author, p.text, u.username, u.email')
            ->getQuery();

        $comments = $query->getResult();
//        $comments = $this->getDoctrine()->getRepository('WykopBundle:BlogComments')->findBy(['postId' => $id]);

        if ($post == NULL) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($post->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najlepsze memy tylko u nas! ' . $post->getTitle())
            ->addMeta('property', 'og:title', $post->getTitle())
            ->addMeta('property', 'og:description', 'Najlepsze memy tylko u nas! ' . $post->getTitle())
            ->addMeta('property', 'og:url', $this->get('router')->generate('mikroblog.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/mikroblog/news.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
}
