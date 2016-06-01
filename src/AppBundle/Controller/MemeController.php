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

class MemeController extends Controller
{
    /**
     * @Route("/meme/{page}", name="meme")
     */
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Meme');
        $query = $em->createQueryBuilder('p')
            ->where('p.status > 0')
            ->where('p.id < :max')
            ->setParameter('max', $page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()->setMaxResults(10);

        $meme = $query->getResult();

        return $this->render('meme/index.html.twig', ['meme' => $meme]);
    }

    /**
     * @Route("/meme/img/{id}", name="meme.id")
     */
    public function memAction()
    {
        if (isset($_GET['url']) && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_GET['url'])) {
            return $this->redirectToRoute('homepage');
        }
        return $this->redirect($_GET['url']);
    }

    /**
     * @Route("/meme/add", name="meme.add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, array('label' => 'Adres RSS', 'required' => true))
            ->add('name', TextType::class, array('label' => 'Nazwa', 'required' => true))
            ->add('css', TextType::class, array('label' => 'Kolor', 'required' => true, 'attr' => ['class' => 'jscolor']))
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
}
