<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Model\SitemapIterator;

class DefaultController extends Controller
{

    protected $color = "green";

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

        $meme = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['promoted' => true], ['id' => 'DESC']);

        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->createQueryBuilder('m')
            ->where('m.promoted = 1')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(8)
            ->getQuery()->getResult();

        $avatar = ($this->getUser()) ? md5($this->getUser()->getEmail()) : md5('thejestemgraczemsquad@gmail.com');

        return $this->render('default/index.html.twig', [
            'color' => $this->color,
            'meme' => $meme,
            'video' => $video,
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

        $em = $this->getDoctrine()->getRepository('AppBundle:User');

        $user = $em->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.username',
                'p.twitch',
                'p.partner',
                'p.description',
                'p.email',
                'p.steam',
                'p.battlenet',
                'p.lol',
                'p.steam',
                'p.localization',
                'p.profilePicturePath'
            )
            ->where('p.username = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();

        if (!$user) {
            throw $this->createNotFoundException('Nie ma takiego użytkownika!');
        }

        $meme = $this->getDoctrine()->getRepository('AppBundle:Meme')->findOneBy(['user' => $user['id']]);
        $video = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['user' => $user['id']]);

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Profil: ' . $user['username'] . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Profil użytkownika " . $user['username'] . " na portalu JestemGraczem.pl")
            ->addMeta('property', 'og:title', $user['username'])
            ->addMeta('property', 'og:type', 'profile')
            ->addMeta('property', 'og:url', $this->get('router')->generate('user', ['user' => $user['username']], UrlGeneratorInterface::ABSOLUTE_URL));

        $avatar = md5($user['email']);

        return $this->render('default/user.html.twig', [
            'color' => $this->color,
            'user' => $user,
            'avatar' => $avatar,
            'meme' => $meme,
            'video' => $video
        ]);
    }

}
