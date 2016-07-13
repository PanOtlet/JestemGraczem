<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Strona główna :: JestemGraczem.pl')
            ->addMeta('name', 'description', "JestemGraczem.pl jest to pierwszy w Polsce portal poświęcony graczom, a nie samym grom!")
            ->addMeta('property', 'og:title', 'Strona główna :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $meme = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['status' => 2], ['id' => 'DESC']);
        $stream = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(['partner' => 1]);

        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->createQueryBuilder('m')
            ->where('m.accept = 1')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()->getResult();

        $avatar = ($this->getUser()) ? md5($this->getUser()->getEmail()) : 23;

        return $this->render('default/index.html.twig', [
            'meme' => $meme,
            'video' => $video,
            'stream' => $stream,
            'avatar' => $avatar
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

        $meme = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['user' => $user->getId()]);
        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['user' => $user->getId()]);

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Profil: ' . $user->getUsername() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Profil użytkownika " . $user->getUsername() . " na portalu JestemGraczem.pl")
            ->addMeta('property', 'og:title', $user->getUsername())
            ->addMeta('property', 'og:type', 'profile')
            ->addMeta('property', 'og:url', $this->get('router')->generate('user', ['user' => $user], UrlGeneratorInterface::ABSOLUTE_URL));

        $avatar = md5($user->getEmail());

        return $this->render('default/user.html.twig', [
            'user' => $user,
            'avatar' => $avatar,
            'meme' => $meme,
            'video' => $video
        ]);
    }

}
