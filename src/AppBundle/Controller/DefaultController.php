<?php

namespace AppBundle\Controller;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use ForumBundle\Entity\BlogPosts;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Strona główna :: JestemGraczem.pl')
            ->addMeta('name', 'description', "JestemGraczem.pl jest to pierwszy w Polsce portal poświęcony graczom, a nie samym grom!")
            ->addMeta('property', 'og:title', 'Strona główna :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $articles = $this->getDoctrine()->getRepository('NewsBundle:News')->createQueryBuilder('m')
            ->where('m.promoted = 1')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()->getResult();


        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM meme WHERE RAND()<(SELECT ((10/COUNT(*))*10) FROM meme) AND meme.accept = 1 ORDER BY RAND() LIMIT 1");
        $statement->execute();
        $mem = $statement->fetch();

        $statement = $connection->prepare("SELECT * FROM video WHERE RAND()<(SELECT ((10/COUNT(*))*10) FROM video) AND video.promoted = 1 ORDER BY RAND() LIMIT 1");
        $statement->execute();
        $video = $statement->fetch();

        $featuredEvents = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT e FROM AppBundle:FeaturedEvents e WHERE e.startDate < CURRENT_TIMESTAMP() AND e.date > CURRENT_TIMESTAMP()')
            ->getResult();

        return $this->render('default/index.html.twig', [
            'articles' => $articles,
            'mem' => $mem,
            'video' => $video,
            'events' => $featuredEvents
        ]);
    }

    /**
     * @Route("/admin", name="adminFakePanel")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminFakeAction()
    {
        return $this->redirect('http://stackoverflow.com/admin.php');
    }

    /**
     * @Route("/redirect", name="redirect", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectAction()
    {
        if (isset($_GET['url']) && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_GET['url'])) {
            return $this->redirectToRoute('homepage');
        }

        if (isset($_GET['r']) && $_GET['r'] == TRUE) {
            return $this->redirect($_GET['url']);
        }

        return $this->render('default/frame.html.twig', [
            'url' => $_GET['url'],
        ]);
    }

    /**
     * @Route("/u/{user}", name="user")
     * @param $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userSiteAction($user)
    {

        $em = $this->getDoctrine()->getRepository('AppBundle:User');

        $user = $em->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.username',
                'p.twitch',
                'p.beampro',
                'p.youtube',
                'p.partner',
                'p.premium',
                'p.editor',
                'p.description',
                'p.email',
                'p.steam',
                'p.battlenet',
                'p.lol',
                'p.steam',
                'p.wot',
                'p.roles',
                'p.localization',
                'p.profilePicturePath'
            )
            ->where('p.username = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Nie ma takiego użytkownika!');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Profil: ' . $user['username'] . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Profil użytkownika " . $user['username'] . " na portalu JestemGraczem.pl")
            ->addMeta('property', 'og:title', $user['username'])
            ->addMeta('property', 'og:type', 'profile')
            ->addMeta('property', 'og:url', $this->get('router')->generate('user', ['user' => $user['username']], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('default/user.html.twig', [
            'user' => $user
        ]);
    }

}
