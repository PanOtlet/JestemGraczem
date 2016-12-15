<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\News;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NewsController extends Controller
{

    protected $color = "orange";

    /**
     * @Route("/rss", name="rss")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render($this->getParameter('theme') . '/default/news.html.twig', ['rss' => []]);
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najnowsze newsy ze świata gier! :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najnowsze i najciekawsze newsy ze świata zgromadzonego wokół gier komputerowych!')
            ->addMeta('property', 'og:title', 'Najnowsze newsy ze świata gier!')
            ->addMeta('property', 'og:type', 'article')
            ->addMeta('property', 'og:description', 'Najnowsze i najciekawsze newsy ze świata zgromadzonego wokół gier komputerowych!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('rss', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $rss = $this->getDoctrine()->getRepository('AppBundle:News')->findBy(['user' => $this->getUser()->getId()]);
        return $this->render($this->getParameter('theme') . '/default/news.html.twig', [
            'color' => $this->color,
            'rss' => $rss
        ]);
    }

    /**
     * @Route("/rss/add", name="rss.add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newsAddAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['label' => 'news.rss', 'required' => true])
            ->add('name', TextType::class, ['label' => 'news.name', 'required' => true])
            ->add('css', TextType::class, ['label' => 'news.color', 'required' => true, 'attr' => ['class' => 'jscolor']])
            ->add('save', SubmitType::class, ['label' => 'news.add'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = new News();
            $data->setUser($this->getUser()->getId());
            $data->setName($form->get('name')->getViewData());
            $data->setUrl($form->get('url')->getViewData());
            $data->setCss($form->get('css')->getViewData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano prywatny kanał informacji!'
            );
            return $this->redirectToRoute('rss');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj swój własny news! :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj swóje własne kanały z newsami, byś zawsze był na bieżąco!')
            ->addMeta('property', 'og:title', 'Dodaj swój własny news!')
            ->addMeta('property', 'og:description', 'Dodaj swóje własne kanały z newsami, byś zawsze był na bieżąco!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('rss.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/default/newsadd.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/rss/remove/{id}", name="rss.remove")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newsRemoveAction($id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:News')->findOneBy(['id' => $id]);

        if (!$user) {
            $this->addFlash(
                'error',
                'Nie ma takiego kanału informacji!'
            );

            return $this->redirectToRoute('homepage');
        }

        if ($user->getUser() != $this->getUser()->getId()) {
            $this->addFlash(
                'warning',
                'Można kasować tylko swoje kanały informacji!'
            );
            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            'Usunięto kanał informacji!'
        );
        return $this->redirectToRoute('homepage');
    }

}
