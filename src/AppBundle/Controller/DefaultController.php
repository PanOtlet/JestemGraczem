<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\News;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/redirect", name="redirect")
     */
    public function redirectAction()
    {
        if (isset($_GET['url']) && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_GET['url'])){
            return $this->redirectToRoute('homepage');
        }
        return $this->redirect($_GET['url']);
    }

    /**
     * @Route("/news/add", name="news.add")
     */
    public function newsAddAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, array('label' => 'Adres RSS'))
            ->add('name', TextType::class, array('label' => 'Nazwa'))
            ->add('css', TextType::class, array('label' => 'Kolor', 'attr' => ['class'=>'jscolor']))
            ->add('save', SubmitType::class, array('label' => 'Dodaj kanał'))
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
            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/newsadd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/u/{user}", name="user")
     */
    public function userSiteAction($user)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $user]);

        if (!$user) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/user.html.twig', ['user' => $user]);
    }

}
