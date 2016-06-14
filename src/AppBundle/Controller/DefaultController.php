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
    public function indexAction()
    {
        $meme = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['status' => 2], ['id' => 'DESC']);
        $stream = $this->getDoctrine()->getRepository('AppBundle:Stream')->findBy(['status' => 2], ['id' => 'DESC']);

        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->createQueryBuilder('m')
            ->where('m.status = 2')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()->getResult();

        return $this->render('default/index.html.twig', [
            'meme' => $meme,
            'video' => $video,
            'stream' => $stream
        ]);
    }

    /**
     * @Route("/redirect", name="redirect")
     */
    public function redirectAction()
    {
        if (isset($_GET['url']) && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_GET['url'])) {
            return $this->redirectToRoute('homepage');
        }
        return $this->redirect($_GET['url']);
    }

    /**
     * @Route("/u/{user}", name="user")
     */
    public function userSiteAction($user)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $user]);

        if (!$user) {
            throw $this->createNotFoundException('Nie ma takiego użytkownika!');
        }

        $avatar = md5($user->getEmail());

        return $this->render('default/user.html.twig', ['user' => $user, 'avatar' => $avatar]);
    }

}
