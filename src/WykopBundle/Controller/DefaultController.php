<?php

namespace WykopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mikroblog")
     */
    public function indexAction($page = 0)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Mikroblog :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Mikroblog to miejsce dla każdego gracza do wyrażania siebie, swoich emocji i rozmowy na tematy dotyczące gier! JestemGraczem.pl")
            ->addMeta('property', 'og:title', 'Mikroblog :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('mikroblog', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/mikroblog/index.html.twig', [

        ]);
    }
}
