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
    /**
     * @Route("/news", name="news")
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render('default/news.html.twig', ['rss' => []]);
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najnowsze newsy ze świata gier! :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najnowsze i najciekawsze newsy ze świata zgromadzonego wokół gier komputerowych!')
            ->addMeta('property', 'og:title', 'Najnowsze newsy ze świata gier!')
            ->addMeta('property', 'og:type', 'article')
            ->addMeta('property', 'og:description', 'Najnowsze i najciekawsze newsy ze świata zgromadzonego wokół gier komputerowych!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('news', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $rss = $this->getDoctrine()->getRepository('AppBundle:News')->findBy(['user' => $this->getUser()->getId()]);
        return $this->render('default/news.html.twig', [
            'rss' => $rss
        ]);
    }

    /**
     * @Route("/news/add", name="news.add")
     */
    public function newsAddAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['label' => 'Adres RSS', 'required' => true])
            ->add('name', TextType::class, ['label' => 'Nazwa', 'required' => true])
            ->add('css', TextType::class, ['label' => 'Kolor', 'required' => true, 'attr' => ['class' => 'jscolor']])
            ->add('save', SubmitType::class, ['label' => 'Dodaj kanał'])
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
                'danger',
                'Dodano prywatny kanał informacji!'
            );
            return $this->redirectToRoute('news');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj swój własny news! :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj swóje własne kanały z newsami, byś zawsze był na bieżąco!')
            ->addMeta('property', 'og:title', 'Dodaj swój własny news!')
            ->addMeta('property', 'og:description', 'Dodaj swóje własne kanały z newsami, byś zawsze był na bieżąco!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('news.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('default/newsadd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/remove/{id}", name="news.remove")
     */
    public function newsRemoveAction($id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:News')->findOneBy(['id' => $id]);

        if (!$user) {
            $this->addFlash(
                'danger',
                'Nie ma takiego kanału informacji!'
            );

            return $this->redirectToRoute('homepage');
        }

        if ($user->getUser() != $this->getUser()->getId()) {
            $this->addFlash(
                'danger',
                'Można kasować tylko swoje kanały informacji!'
            );
            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'danger',
            'Usunięto kanał informacji!'
        );
        return $this->redirectToRoute('homepage');
    }

}
