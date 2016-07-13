<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CMSController extends Controller
{

    protected $color = "red";

    /**
     * @Route("/{title}", name="cms")
     */
    public function indexAction($title = NULL)
    {
        $cms = $this->getDoctrine()->getRepository('AppBundle:CMS')->findOneBy(['url' => $title]);

        if ($title == NULL || $cms == NULL) {
            throw $this->createNotFoundException('Strony nie znaleziono!');
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($cms->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', substr($cms->getText(), 0, 150))
            ->addMeta('property', 'og:title', $cms->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('cms', ['title' => $cms->getUrl()], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('CMS/index.html.twig', [
            'color' => $this->color,
            'cms' => $cms
        ]);
    }

}
